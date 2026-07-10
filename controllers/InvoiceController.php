<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/InvoiceModel.php';
require_once __DIR__ . '/../models/SalesOrderModel.php';

class InvoiceController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new InvoiceModel();
        $this->viewPath = 'invoicing';
    }

    public function index(): void {
        $this->render('index', ['invoices' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'InvoiceID' => $this->getInput('InvoiceID'),
                'InvoiceDate' => $this->getInput('invoice_date'),
                'Amount' => (float)$this->getInput('amount', '0'),
                'Tax' => (float)$this->getInput('tax', '0'),
                'TotalDue' => (float)$this->getInput('total_due', '0'),
                'PaymentStatus' => $this->getInput('payment_status', 'Unpaid'),
                'DueDate' => $this->getInput('due_date'),
                'OrderID' => $this->getInput('order_id'),
            ]);
            setFlash('success', 'Invoice created.');
            $this->redirect('invoicing');
            return;
        }
        $this->render('form', ['orders' => (new SalesOrderModel())->all()]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $inv = $this->model->find($id);
        if (!$inv) { setFlash('error', 'Not found.'); $this->redirect('invoicing'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'InvoiceDate' => $this->getInput('invoice_date'),
                'Amount' => (float)$this->getInput('amount', '0'),
                'Tax' => (float)$this->getInput('tax', '0'),
                'TotalDue' => (float)$this->getInput('total_due', '0'),
                'PaymentStatus' => $this->getInput('payment_status'),
                'DueDate' => $this->getInput('due_date'),
                'OrderID' => $this->getInput('order_id'),
            ]);
            setFlash('success', 'Invoice updated.');
            $this->redirect('invoicing');
            return;
        }
        $this->render('form', ['invoice' => $inv, 'orders' => (new SalesOrderModel())->all()]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Invoice deleted.');
        $this->redirect('invoicing');
    }
}
