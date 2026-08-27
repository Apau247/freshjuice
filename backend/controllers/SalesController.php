<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SalesOrderModel.php';
require_once __DIR__ . '/../models/CustomerModel.php';
require_once __DIR__ . '/../models/FinishedGoodModel.php';
require_once __DIR__ . '/../models/PricingModel.php';

class SalesController extends Controller {
    private const STATUSES = ['Pending', 'Processing', 'Completed', 'Cancelled'];
    /** Ghana VAT applied on every sale (matches the rate shown on the order screen). */
    private const VAT_RATE = 0.15;

    public function __construct() {
        parent::__construct();
        $this->model = new SalesOrderModel();
        $this->viewPath = 'sales';
    }

    /**
     * Parse the JSON cart posted by the POS order screen.
     * Each line: {fg_id, quantity, unit_price}. Malformed lines are dropped;
     * remaining problems surface as validation errors below.
     */
    private function parseCart(string $json): array {
        $lines = json_decode($json, true);
        if (!is_array($lines)) return [];
        $cart = [];
        foreach ($lines as $l) {
            if (!is_array($l)) continue;
            $fgId  = trim((string)($l['fg_id'] ?? ''));
            $qty   = (float)($l['quantity'] ?? 0);
            $price = (float)($l['unit_price'] ?? 0);
            if ($fgId === '' || !is_finite($qty) || $qty <= 0 || !is_finite($price) || $price < 0) continue;
            $cart[] = ['fg_id' => $fgId, 'quantity' => $qty, 'unit_price' => $price];
        }
        return $cart;
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
        // Multi-line POS orders arrive as ids=A,B,C; single orders still use id=A.
        $idsParam = trim((string)$this->getInput('ids'));
        $single   = trim((string)$this->getInput('id'));
        $ids = $idsParam !== ''
            ? array_values(array_filter(array_map('trim', explode(',', $idsParam))))
            : ($single !== '' ? [$single] : []);
        $ids = array_slice(array_unique($ids), 0, 100); // sanity cap

        if (!$ids) { setFlash('error', 'Order not found.'); $this->redirect('sales'); return; }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = $this->model->query(
            "SELECT so.*, c.Name AS CustomerName, c.Phone AS CustomerPhone,
                    c.Email AS CustomerEmail, fg.Flavour AS FG_Flavour,
                    fg.Unit AS FG_Unit, u.Name AS ServedBy
             FROM sales_orders so
             LEFT JOIN customers c ON so.CustomerID = c.CustomerID
             LEFT JOIN finished_goods fg ON so.FG_ID = fg.FG_ID
             LEFT JOIN users u ON so.CreatedBy = u.UserID
             WHERE so.OrderID IN ($placeholders)",
            $ids
        );

        // Preserve the requested order (IN() doesn't guarantee it).
        $byId = [];
        foreach ($rows as $r) { $byId[(string)$r['OrderID']] = $r; }
        $orders = [];
        foreach ($ids as $id) { if (isset($byId[$id])) $orders[] = $byId[$id]; }

        if (!$orders) { setFlash('error', 'Order not found.'); $this->redirect('sales'); return; }

        logAudit($_SESSION['user_id'] ?? null, 'PRINT', 'Sales', implode(',', array_slice($ids, 0, 3)), count($orders) > 1 ? 'Printed combined receipt (' . count($orders) . ' items)' : 'Printed receipt');
        $this->renderPrint('receipt', ['orders' => $orders, 'order' => $orders[0]], 'Sales Receipt');
    }

    public function index(): void {
        $this->render('index', ['orders' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status    = $this->getInput('status', 'Pending');
            $customer  = $this->getInput('customer_id');
            $orderDate = $this->getInput('order_date');
            $payment   = trim((string)$this->getInput('payment_method'));
            $notes     = mb_substr($this->getInput('notes'), 0, 500);
            $cart      = $this->parseCart((string)($_POST['cart_items'] ?? ''));

            // Legacy single-line fallback (old bookmarks / integrations).
            if (empty($cart)) {
                $fgId  = $this->getInput('fg_id');
                $qty   = (float)$this->getInput('quantity', '0');
                $total = (float)$this->getInput('total_amount', '0');
                if ($fgId !== '' && $qty > 0) {
                    // Stored TotalAmount is VAT-inclusive; recover the pre-tax unit price.
                    $unit = $total > 0 ? ($total / (1 + self::VAT_RATE)) / $qty : 0;
                    $cart = [['fg_id' => $fgId, 'quantity' => $qty, 'unit_price' => round($unit, 4)]];
                }
            }

            // ── Validation ──
            $errors = [];
            if ($orderDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $orderDate)) {
                $errors[] = 'Order date is required and must be a valid date.';
            } elseif ($orderDate > date('Y-m-d')) {
                $errors[] = 'Order date cannot be in the future.';
            }
            $custModel = new CustomerModel();
            if ($customer === '' || !$custModel->find($customer)) {
                $errors[] = 'Please select a valid customer.';
            }
            if (!in_array($status, self::STATUSES, true)) {
                $errors[] = 'Invalid order status.';
            }

            $fgModel = new FinishedGoodModel();
            foreach ($cart as $i => $line) {
                if (!$fgModel->find($line['fg_id'])) {
                    $errors[] = 'Line ' . ($i + 1) . ': selected finished goods item does not exist.';
                    continue;
                }
                if ($line['quantity'] > 1000000) {
                    $errors[] = 'Line ' . ($i + 1) . ': quantity is unrealistically large.';
                }
            }
            if (empty($cart)) {
                $errors[] = 'The order is empty — add at least one product.';
            }

            // Aggregate demand per product so two cart lines of the same FG
            // can't sneak past available stock.
            if (!$errors && $status === 'Completed') {
                $demand = [];
                foreach ($cart as $line) {
                    $demand[$line['fg_id']] = ($demand[$line['fg_id']] ?? 0) + $line['quantity'];
                }
                foreach ($demand as $fgId => $qtyWanted) {
                    $fg = $fgModel->find($fgId);
                    if ($fg && (float)$fg['QuantityAvailable'] < $qtyWanted) {
                        $errors[] = 'Insufficient stock for "' . ($fg['Flavour'] ?? $fgId) . '". Available: ' . $fg['QuantityAvailable'];
                    }
                }
            }

            if ($errors) {
                setFlash('error', implode(' ', $errors));
                $this->redirect('sales/create');
                return;
            }

            // ── Persist: one sales_orders row per cart line, atomically ──
            $db = getDb();
            $db->beginTransaction();
            try {
                $createdIds = [];
                $n = count($cart);
                foreach ($cart as $idx => $line) {
                    do { $id = generateId('ORD'); } while ($this->model->find($id));

                    $net = round($line['quantity'] * $line['unit_price'], 2);
                    $vat = round($net * self::VAT_RATE, 2);
                    $lineNotes = trim($notes);
                    if ($n > 1) $lineNotes = trim('[Item ' . ($idx + 1) . " of $n] " . $lineNotes);
                    if ($payment !== '') $lineNotes = trim($lineNotes . ' [Payment: ' . $payment . ']');

                    if ($status === 'Completed') {
                        // Lock the FG row to prevent TOCTOU race conditions.
                        $locked = $fgModel->lockForStock($line['fg_id']);
                        if (!$locked || (float)$locked['QuantityAvailable'] < $line['quantity']) {
                            $db->rollBack();
                            setFlash('error', 'Insufficient stock for "' . ($locked['Flavour'] ?? $line['fg_id'])
                                . '". Available: ' . ($locked['QuantityAvailable'] ?? 0));
                            $this->redirect('sales/create');
                            return;
                        }
                        $fgModel->reduceStock($line['fg_id'], $line['quantity']);
                    }

                    $this->model->create([
                        'OrderID'     => $id,
                        'OrderDate'   => $orderDate,
                        'TotalAmount' => round($net + $vat, 2),
                        'Quantity'    => $line['quantity'],
                        'Status'      => $status,
                        'CustomerID'  => $customer,
                        'FG_ID'       => $line['fg_id'],
                        'CreatedBy'   => $_SESSION['user_id'] ?? null,
                        'Notes'       => $lineNotes,
                    ]);
                    $createdIds[] = $id;
                }

                $db->commit();
                logAudit($_SESSION['user_id'], 'CREATE', 'Sales', implode(',', array_slice($createdIds, 0, 3)), 'Created POS order with ' . $n . ' line(s)');
                setFlash('success', 'Order created (' . $n . ' item' . ($n > 1 ? 's' : '') . ').');
                // Smooth flow: go straight to the printable receipt after a purchase.
                $this->redirect('sales/receipt&ids=' . urlencode(implode(',', $createdIds)));
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
            // Admin-set default prices per product (Product Prices screen).
            'priceMap' => (new PricingModel())->getPriceMapByFgId(),
            // Recent customers / products first, so repeat selling is one click.
            'trends' => [
                'customer' => $this->trendIds('sales_orders', 'CustomerID', 'OrderDate'),
                'fg'       => $this->trendIds('sales_orders', 'FG_ID', 'OrderDate'),
                'status'   => $this->trendIds('sales_orders', 'Status', 'OrderDate'),
            ],
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
                    $fg = $fgModel->lockForStock($newFgId);
                    if (!$fg || (float)$fg['QuantityAvailable'] < $qty) {
                        $db->rollBack();
                        setFlash('error', 'Insufficient finished goods stock. Available: ' . ($fg['QuantityAvailable'] ?? 0));
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
