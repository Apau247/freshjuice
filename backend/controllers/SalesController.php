<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SalesOrderModel.php';
require_once __DIR__ . '/../models/CustomerModel.php';
require_once __DIR__ . '/../models/FinishedGoodModel.php';

class SalesController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new SalesOrderModel();
        $this->viewPath = 'sales';
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
            $customer = $this->getInput('customer_id');

            if (empty($customer)) {
                setFlash('error', 'Customer is required.');
                $this->redirect('sales');
                return;
            }
            if ($qty <= 0) {
                setFlash('error', 'Quantity must be greater than zero.');
                $this->redirect('sales');
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
                        $this->redirect('sales');
                        return;
                    }
                    $fgModel->reduceStock($fgId, $qty);
                }

                $this->model->create([
                    'OrderID' => $id, 'OrderDate' => $this->getInput('order_date'),
                    'TotalAmount' => (float)$this->getInput('total_amount', '0'),
                    'Quantity' => $qty, 'Status' => $status,
                    'CustomerID' => $customer,
                    'FG_ID' => $fgId ?: null,
                    'CreatedBy' => $_SESSION['user_id'] ?? null,
                    'Notes' => $this->getInput('notes'),
                ]);

                $db->commit();
                logAudit($_SESSION['user_id'], 'CREATE', 'Sales', $id, 'Created sales order');
                setFlash('success', 'Order created.');
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
            $newStatus = $this->getInput('status', (string)$order['Status']);
            $newFgId = $this->getInput('fg_id') ?: null;

            if (empty($customer)) {
                setFlash('error', 'Customer is required.');
                $this->redirect('sales');
                return;
            }
            if ($qty <= 0) {
                setFlash('error', 'Quantity must be greater than zero.');
                $this->redirect('sales');
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
                        $this->redirect('sales');
                        return;
                    }
                    $fgModel->reduceStock($newFgId, $qty);
                }

                $this->model->update($id, [
                    'OrderDate' => $this->getInput('order_date'),
                    'TotalAmount' => (float)$this->getInput('total_amount', '0'),
                    'Quantity' => $qty,
                    'Status' => $newStatus,
                    'CustomerID' => $customer,
                    'FG_ID' => $newFgId,
                    'Notes' => $this->getInput('notes'),
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
