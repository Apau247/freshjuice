<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/PackagingMaterialModel.php';

class PackagingMaterialController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new PackagingMaterialModel();
        $this->viewPath = 'materials';
    }

    public function index(): void {
        $this->render('packaging_materials', ['materials' => $this->model->all()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'PackageID' => $this->getInput('PackageID'), 'Name' => $this->getInput('name'),
                'Type' => $this->getInput('type'), 'Unit' => $this->getInput('unit', 'pcs'),
                'CurrentStock' => (float)$this->getInput('current_stock', '0'),
                'MinStock' => (float)$this->getInput('min_stock', '0'),
            ]);
            setFlash('success', 'Packaging material created.');
            $this->redirect('materials/packaging');
            return;
        }
        $this->render('pkg_form');
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('materials/packaging'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'Name' => $this->getInput('name'), 'Type' => $this->getInput('type'),
                'Unit' => $this->getInput('unit'),
                'CurrentStock' => (float)$this->getInput('current_stock', '0'),
                'MinStock' => (float)$this->getInput('min_stock', '0'),
            ]);
            setFlash('success', 'Packaging material updated.');
            $this->redirect('materials/packaging');
            return;
        }
        $this->render('pkg_form', ['material' => $item]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Packaging material deleted.');
        $this->redirect('materials/packaging');
    }
}
