<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SupplierModel.php';

class SupplierController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new SupplierModel();
        $this->viewPath = 'suppliers';
    }

    public function index(): void {
        $this->render('index', ['suppliers' => $this->model->all()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->getInput('SupplierID');
            $name = $this->getInput('name');
            if (empty($id) || empty($name)) {
                setFlash('error', 'Supplier ID and Name are required.');
                $this->redirect('suppliers');
                return;
            }
            $this->model->create([
                'SupplierID' => $id, 'Name' => $name,
                'Contact' => $this->getInput('contact_person'), 'Email' => $this->getInput('email'),
                'Phone' => $this->getInput('phone'), 'Address' => $this->getInput('address'),
                'Type' => $this->getInput('category', 'Fruit Supplier'),
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Suppliers', $id, 'Created supplier: ' . $name);
            setFlash('success', 'Supplier created successfully.');
            $this->redirect('suppliers');
            return;
        }
        $this->render('form');
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $supplier = $this->model->find($id);
        if (!$supplier) { setFlash('error', 'Not found.'); $this->redirect('suppliers'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'Name' => $this->getInput('name'), 'Contact' => $this->getInput('contact_person'),
                'Email' => $this->getInput('email'), 'Phone' => $this->getInput('phone'),
                'Address' => $this->getInput('address'), 'Type' => $this->getInput('category'),
                'Status' => $this->getInput('status'),
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Suppliers', $id, 'Updated supplier');
            setFlash('success', 'Supplier updated.');
            $this->redirect('suppliers');
            return;
        }
        $this->render('form', ['supplier' => $supplier]);
    }

    public function delete(): void {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Suppliers', $id, 'Deleted supplier');
        setFlash('success', 'Supplier deleted.');
        $this->redirect('suppliers');
    }

    public function deliveries(): void {
        $this->render('deliveries', [
            'deliveries' => $this->model->getAllDeliveries(),
            'suppliers' => $this->model->all(),
        ]);
    }

    public function createDelivery(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->getInput('DeliveryID');
            $this->model->create([
                'DeliveryID' => $id, 'SupplierID' => $this->getInput('supplier_id'),
                'DeliveryDate' => $this->getInput('delivery_date'),
                'ItemName' => $this->getInput('item_name'),
                'Quantity' => (float)$this->getInput('quantity', '0'),
                'Unit' => $this->getInput('unit', 'kg'),
                'QualityGrade' => $this->getInput('quality_grade', 'Grade A'),
                'ReceivedBy' => $_SESSION['user_id'] ?? null,
                'Notes' => $this->getInput('notes'), 'Status' => $this->getInput('status', 'Received'),
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Deliveries', $id, 'Recorded delivery');
            setFlash('success', 'Delivery recorded.');
            $this->redirect('suppliers/deliveries');
            return;
        }
        $this->render('delivery_form', ['suppliers' => $this->model->all()]);
    }

    public function editDelivery(): void {
        $id = $this->getInput('id');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'SupplierID' => $this->getInput('supplier_id'),
                'DeliveryDate' => $this->getInput('delivery_date'),
                'ItemName' => $this->getInput('item_name'),
                'Quantity' => (float)$this->getInput('quantity', '0'),
                'Unit' => $this->getInput('unit'), 'QualityGrade' => $this->getInput('quality_grade'),
                'Notes' => $this->getInput('notes'), 'Status' => $this->getInput('status'),
            ]);
            setFlash('success', 'Delivery updated.');
            $this->redirect('suppliers/deliveries');
            return;
        }
        $this->render('delivery_form', [
            'delivery' => $this->model->find($id),
            'suppliers' => $this->model->all(),
        ]);
    }

    public function deleteDelivery(): void {
        $id = $this->getInput('id');
        $this->model->delete($id);
        setFlash('success', 'Delivery deleted.');
        $this->redirect('suppliers/deliveries');
    }
}
