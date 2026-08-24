<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SalesOrderModel.php';
require_once __DIR__ . '/../models/CustomerModel.php';
require_once __DIR__ . '/../models/FinishedGoodModel.php';

class SalesController extends Controller {
    private const STATUSES = ['Pending', 'Processing', 'Completed', 'Cancelled'];

    public function __construct() {
        parent::__construct();
        $this->model = new SalesOrderModel();
        $this->viewPath = 'sales';
    }

    /**
     * Validate one submitted sale. Returns a list of human-readable errors --
     * every field is checked server-side so bad data never reaches the DB even
     * if client-side validation is bypassed.
     */
    private function validateSale(string $orderDate, string $customer, float $qty, float $total, string $status, string $fgId): array {
        $errors = [];
        $custModel = new CustomerModel();
        $fgModel   = new FinishedGoodModel();

        if ($orderDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $orderDate) || !checkdate((int)substr($orderDate, 5, 2), (int)substr($orderDate, 8, 2), (int)substr($orderDate, 0, 4))) {
            $errors[] = 'Order date is required and must be a valid date.';
        } elseif ($orderDate > date('Y-m-d')) {
            $errors[] = 'Order date cannot be in the future.';
        }
        if ($customer === '' || !$custModel->find($customer)) {
            $errors[] = 'Please select a valid customer.';
        }
        if (!is_finite($qty) || $qty <= 0) {
            $errors[] = 'Quantity must be a number greater than zero.';
        } elseif ($qty > 1000000) {
            $errors[] = 'Quantity is unrealistically large.';
        }
        if (!is_finite($total) || $total < 0) {
            $errors[] = 'Total amount cannot be negative.';
        }
        if (!in_array($status, self::STATUSES, true)) {
            $errors[] = 'Invalid order status.';
        }
        if ($fgId !== '' && !$fgModel->find($fgId)) {
            $errors[] = 'Selected finished goods item does not exist.';
        }
        return $errors;
    }

    /** Printable receipt for a completed purchase (company header + items only). */
    public function receipt(): void {
        $id = $this->getInput('id');
        $rows = $this->model->query(
            "SELECT so.*, c.Name AS CustomerName, c.Phone AS CustomerPhone,
                    c.Email AS CustomerEmail, fg.Flavour AS FG_Flavour,
                    fg.Unit AS FG_Unit, u.Name AS ServedBy
             FROM sales_orders so
             LEFT JOIN customers c ON so.CustomerID = c.CustomerID
             LEFT JOIN finished_goods fg ON so.FG_ID = fg.FG_ID
             LEFT JOIN users u ON so.CreatedBy = u.UserID
             WHERE so.OrderID = ? LIMIT 1",
            [$id]
        );
        $order = $rows[0] ?? null;
        if (!$order) { setFlash('error', 'Order not found.'); $this->redirect('sales'); return; }

        logAudit($_SESSION['user_id'] ?? null, 'PRINT', 'Sales', $id, 'Printed receipt');
        $this->renderPrint('receipt', ['order' => $order], 'Sales Receipt');
    }

    public function index(): void {
        $this->render('index', ['orders' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->getInput('OrderID') ?: generateId('ORD');
            $status = $this->getInput('status', 'Pending');
            $fgId = $this->getInput('fg_id');
            $qty = (float)$this->getInput('quantity', '0');
            $total = (float)$this->getInput('total_amount', '0');
            $customer = $this->getInput('customer_id');
            $orderDate = $this->getInput('order_date');

            $errors = $this->validateSale($orderDate, $customer, $qty, $total, $status, $fgId);
            if ($errors) {
                setFlash('error', implode(' ', $errors));
                $this->redirect('sales/create');
                return;
            }

            $db = getDb();
            $db->beginTransaction();
            try {
                if ($status === 'Completed' && $fgId && $qty > 0) {
                    $fgModel = new FinishedGoodModel();
                    $fg = $fgModel->find($fgId);
                    if ($fg && $fg['QuantityAvailable'] < $qty) {
                        $db->rollBack();
                        setFlash('error', 'Insufficient finished goods stock. Available: ' . $fg['QuantityAvailable']);
                        $this->redirect('sales/create');
                        return;
                    }
                    $fgModel->reduceStock($fgId, $qty);
                }

                $this->model->create([
                    'OrderID' => $id, 'OrderDate' => $orderDate,
                    'TotalAmount' => $total,
                    'Quantity' => $qty, 'Status' => $status,
                    'CustomerID' => $customer,
                    'FG_ID' => $fgId ?: null,
                    'CreatedBy' => $_SESSION['user_id'] ?? null,
                    'Notes' => mb_substr($this->getInput('notes'), 0, 500),
                ]);

                $db->commit();
                logAudit($_SESSION['user_id'], 'CREATE', 'Sales', $id, 'Created sales order');
                setFlash('success', 'Order created.');
                // Smooth flow: go straight to the printable receipt after a purchase.
                $this->redirect('sales/receipt&id=' . urlencode($id));
                return;
            } catch (\Exception $e) {
                $db->rollBack();
                error_log('Sales create failed: ' . $e->getMessage());
                setFlash('error', 'Failed to create order. Please try again.');
            }
            $this->redirect('sales');
            return;
        }
        $this->render('form', [
            'customers' => (new CustomerModel())->all(),
            'finishedGoods' => (new FinishedGoodModel())->query("SELECT * FROM finished_goods WHERE QuantityAvailable > 0"),
        ]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $order = $this->model->find($id);
        if (!$order) { setFlash('error', 'Not found.'); $this->redirect('sales'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer = $this->getInput('customer_id', (string)$order['CustomerID']);
            $qty = (float)$this->getInput('quantity', (string)$order['Quantity']);
            $total = (float)$this->getInput('total_amount', (string)$order['TotalAmount']);
            $newStatus = $this->getInput('status', (string)$order['Status']);
            $newFgId = $this->getInput('fg_id') ?: null;
            $orderDate = $this->getInput('order_date');

            $errors = $this->validateSale($orderDate ?: (string)$order['OrderDate'], $customer, $qty, $total, $newStatus, (string)($newFgId ?? ''));
            if ($errors) {
                setFlash('error', implode(' ', $errors));
                $this->redirect('sales/edit&id=' . urlencode($id));
                return;
            }

            // Keep finished-goods stock in step with the order lifecycle:
            // a Completed order holds a reservation, everything else releases it.
            $db = getDb();
            $db->beginTransaction();
            try {
                $fgModel = new FinishedGoodModel();

                if ($order['Status'] === 'Completed' && $order['FG_ID']) {
                    $fgModel->restoreStock((string)$order['FG_ID'], (float)$order['Quantity']);
                }

                if ($newStatus === 'Completed' && $newFgId) {
                    $fg = $fgModel->find($newFgId);
                    if ($fg && (float)$fg['QuantityAvailable'] < $qty) {
                        $db->rollBack();
                        setFlash('error', 'Insufficient finished goods stock. Available: ' . $fg['QuantityAvailable']);
                        $this->redirect('sales/edit&id=' . urlencode($id));
                        return;
                    }
                    $fgModel->reduceStock($newFgId, $qty);
                }

                $this->model->update($id, [
                    'OrderDate' => $orderDate,
                    'TotalAmount' => $total,
                    'Quantity' => $qty,
                    'Status' => $newStatus,
                    'CustomerID' => $customer,
                    'FG_ID' => $newFgId,
                    'Notes' => mb_substr($this->getInput('notes'), 0, 500),
                ]);

                $db->commit();
                logAudit($_SESSION['user_id'], 'UPDATE', 'Sales', $id, 'Updated sales order');
                setFlash('success', 'Order updated.');
            } catch (\Exception $e) {
                $db->rollBack();
                error_log('Sales update failed: ' . $e->getMessage());
                setFlash('error', 'Failed to update order. Please try again.');
            }
            $this->redirect('sales');
            return;
        }
        $this->render('form', [
            'order' => $order,
            'customers' => (new CustomerModel())->all(),
            'finishedGoods' => (new FinishedGoodModel())->all(),
        ]);
    }

    public function delete(): void {
        $id = $this->getInput('id');
        $order = $this->model->find($id);

        $db = getDb();
        $db->beginTransaction();
        try {
            // A deleted Completed order gives its reserved stock back.
            if ($order && $order['Status'] === 'Completed' && $order['FG_ID']) {
                (new FinishedGoodModel())->restoreStock((string)$order['FG_ID'], (float)$order['Quantity']);
            }
            $this->model->delete($id);
            $db->commit();
            logAudit($_SESSION['user_id'], 'DELETE', 'Sales', $id, 'Deleted sales order');
            setFlash('success', 'Order deleted.');
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('Sales delete failed: ' . $e->getMessage());
            setFlash('error', 'Failed to delete order. Please try again.');
        }
        $this->redirect('sales');
    }
}
