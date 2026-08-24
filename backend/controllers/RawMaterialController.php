<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SupplierModel.php';
require_once __DIR__ . '/../models/RawMaterialModel.php';

class RawMaterialController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new RawMaterialModel();
        $this->viewPath = 'materials';
    }

    public function index(): void {
        $this->render('raw_materials', ['materials' => $this->model->getAllWithSuppliers()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentStock = (float)$this->getInput('current_stock', '0');
            $minStock = (float)$this->getInput('min_stock', '0');
            $err = $this->checkNumber('Current stock', $currentStock, 0)
                ?? $this->checkNumber('Minimum stock', $minStock, 0);
            if ($err) {
                setFlash('error', $err);
                $this->redirect('materials/raw/create');
                return;
            }
            $id = generateId('RMW');
            $this->model->create([
                'MaterialID' => $id, 'Name' => $this->getInput('name'),
                'Type' => $this->getInput('type'), 'Unit' => $this->getInput('unit', 'kg'),
                'CurrentStock' => $currentStock,
                'MinStock' => $minStock,
                'SupplierID' => $this->getInput('supplier_id') ?: null,
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Raw Materials', $id, 'Created material');
            setFlash('success', 'Raw material created.');
            $this->redirect('materials/raw');
            return;
        }
        $this->render('raw_form', ['suppliers' => (new SupplierModel())->all()]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('materials/raw'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentStock = (float)$this->getInput('current_stock', '0');
            $minStock = (float)$this->getInput('min_stock', '0');
            $err = $this->checkNumber('Current stock', $currentStock, 0)
                ?? $this->checkNumber('Minimum stock', $minStock, 0);
            if ($err) {
                setFlash('error', $err);
                $this->redirect('materials/raw/edit&id=' . urlencode($id));
                return;
            }
            $this->model->update($id, [
                'Name' => $this->getInput('name'), 'Type' => $this->getInput('type'),
                'Unit' => $this->getInput('unit'),
                'CurrentStock' => $currentStock,
                'MinStock' => $minStock,
                'SupplierID' => $this->getInput('supplier_id') ?: null,
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Raw Materials', $id, 'Updated material');
            setFlash('success', 'Raw material updated.');
            $this->redirect('materials/raw');
            return;
        }
        $this->render('raw_form', ['material' => $item, 'suppliers' => (new SupplierModel())->all()]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Raw material deleted.');
        $this->redirect('materials/raw');
    }
}
