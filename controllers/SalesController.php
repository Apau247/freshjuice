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
            $id = $this->getInput('OrderID');
            $status = $this->getInput('status', 'Pending');
            $fgId = $this->getInput('fg_id');
            $qty = (float)$this->getInput('quantity', '0');

            if ($status === 'Completed' && $fgId && $qty > 0) {
                (new FinishedGoodModel())->reduceStock($fgId, $qty);
            }

            $this->model->create([
                'OrderID' => $id, 'OrderDate' => $this->getInput('order_date'),
                'TotalAmount' => (float)$this->getInput('total_amount', '0'),
                'Quantity' => $qty, 'Status' => $status,
                'CustomerID' => $this->getInput('customer_id'),
                'FG_ID' => $fgId ?: null,
                'CreatedBy' => $_SESSION['user_id'] ?? null,
                'Notes' => $this->getInput('notes'),
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Sales', $id, 'Created sales order');
            setFlash('success', 'Order created.');
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
            $this->model->update($id, [
                'OrderDate' => $this->getInput('order_date'),
                'TotalAmount' => (float)$this->getInput('total_amount', '0'),
                'Status' => $this->getInput('status'),
                'CustomerID' => $this->getInput('customer_id'),
                'FG_ID' => $this->getInput('fg_id') ?: null,
                'Notes' => $this->getInput('notes'),
            ]);
            setFlash('success', 'Order updated.');
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
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Order deleted.');
        $this->redirect('sales');
    }
}
