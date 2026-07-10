<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ProductionBatchModel.php';
require_once __DIR__ . '/../models/RawMaterialModel.php';
require_once __DIR__ . '/../models/PackagingMaterialModel.php';
require_once __DIR__ . '/../models/MachineModel.php';

class ProductionController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new ProductionBatchModel();
        $this->viewPath = 'production';
    }

    public function index(): void {
        $this->render('index', ['batches' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->getInput('BatchID');
            $batchNum = $this->getInput('BatchNumber');
            $qty = (float)$this->getInput('Quantity', '0');
            $rawMat = $this->getInput('RawMaterialID');
            $pkgMat = $this->getInput('PackagingMaterialID');

            if ($rawMat && $qty > 0) {
                (new RawMaterialModel())->updateStock($rawMat, -$qty);
            }
            if ($pkgMat && $qty > 0) {
                (new PackagingMaterialModel())->updateStock($pkgMat, -$qty);
            }

            $this->model->create([
                'BatchID' => $id, 'BatchNumber' => $batchNum,
                'ProductionDate' => $this->getInput('ProductionDate'),
                'Flavour' => $this->getInput('Flavour'), 'Quantity' => $qty,
                'Unit' => $this->getInput('Unit', 'litres'),
                'Status' => $this->getInput('Status', 'Pending'),
                'UserID' => $_SESSION['user_id'] ?? null,
                'RawMaterialID' => $rawMat ?: null,
                'PackagingMaterialID' => $pkgMat ?: null,
                'MachineID' => $this->getInput('MachineID') ?: null,
                'Notes' => $this->getInput('Notes'),
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Production', $id, "Created batch $batchNum");
            setFlash('success', 'Batch created.');
            $this->redirect('production');
            return;
        }
        $this->render('form', [
            'rawMaterials' => (new RawMaterialModel())->all(),
            'pkgMaterials' => (new PackagingMaterialModel())->all(),
            'machines' => (new MachineModel())->all(),
        ]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $batch = $this->model->find($id);
        if (!$batch) { setFlash('error', 'Not found.'); $this->redirect('production'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'Flavour' => $this->getInput('Flavour'),
                'Quantity' => (float)$this->getInput('Quantity', '0'),
                'Status' => $this->getInput('Status'),
                'RawMaterialID' => $this->getInput('RawMaterialID') ?: null,
                'PackagingMaterialID' => $this->getInput('PackagingMaterialID') ?: null,
                'MachineID' => $this->getInput('MachineID') ?: null,
                'Notes' => $this->getInput('Notes'),
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Production', $id, 'Updated batch');
            setFlash('success', 'Batch updated.');
            $this->redirect('production');
            return;
        }
        $this->render('form', [
            'batch' => $batch,
            'rawMaterials' => (new RawMaterialModel())->all(),
            'pkgMaterials' => (new PackagingMaterialModel())->all(),
            'machines' => (new MachineModel())->all(),
        ]);
    }

    public function delete(): void {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Production', $id, 'Deleted batch');
        setFlash('success', 'Batch deleted.');
        $this->redirect('production');
    }
}
