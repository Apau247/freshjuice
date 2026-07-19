<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ProductionBatchModel.php';
require_once __DIR__ . '/../models/RawMaterialModel.php';
require_once __DIR__ . '/../models/PackagingMaterialModel.php';
require_once __DIR__ . '/../models/MachineModel.php';
require_once __DIR__ . '/../models/UserModel.php';

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
            $id = $this->getInput('BatchID') ?: generateId('BAT');
            $batchNum = $this->getInput('batch_number');
            $qty = (float)$this->getInput('quantity', '0');
            $rawMat = $this->getInput('raw_material_id');
            $pkgMat = $this->getInput('packaging_material_id');
            $status = $this->getInput('status', 'Pending');

            if (empty($batchNum)) {
                setFlash('error', 'Batch number is required.');
                $this->redirect('production');
                return;
            }
            if ($qty <= 0) {
                setFlash('error', 'Quantity must be greater than zero.');
                $this->redirect('production');
                return;
            }

            $db = getDb();
            $db->beginTransaction();
            try {
                if ($rawMat && $status !== 'Cancelled') {
                    $rmModel = new RawMaterialModel();
                    $rm = $rmModel->find($rawMat);
                    if ($rm && $rm['CurrentStock'] < $qty) {
                        $db->rollBack();
                        setFlash('error', 'Insufficient raw material stock. Available: ' . $rm['CurrentStock']);
                        $this->redirect('production');
                        return;
                    }
                    $rmModel->updateStock($rawMat, -$qty);
                }
                if ($pkgMat && $status !== 'Cancelled') {
                    $pkgModel = new PackagingMaterialModel();
                    $pkg = $pkgModel->find($pkgMat);
                    if ($pkg && $pkg['CurrentStock'] < $qty) {
                        $db->rollBack();
                        setFlash('error', 'Insufficient packaging material stock. Available: ' . $pkg['CurrentStock']);
                        $this->redirect('production');
                        return;
                    }
                    $pkgModel->updateStock($pkgMat, -$qty);
                }

                $this->model->create([
                    'BatchID' => $id, 'BatchNumber' => $batchNum,
                    'ProductionDate' => $this->getInput('production_date'),
                    'Flavour' => $this->getInput('flavour'), 'Quantity' => $qty,
                    'Unit' => $this->getInput('unit', 'litres'),
                    'Status' => $status,
                    'UserID' => $_SESSION['user_id'] ?? null,
                    'RawMaterialID' => $rawMat ?: null,
                    'PackagingMaterialID' => $pkgMat ?: null,
                    'MachineID' => $this->getInput('machine_id') ?: null,
                    'Notes' => $this->getInput('notes'),
                ]);

                $db->commit();
                logAudit($_SESSION['user_id'], 'CREATE', 'Production', $id, "Created batch $batchNum");
                setFlash('success', 'Batch created successfully.');
            } catch (\Exception $e) {
                $db->rollBack();
                error_log('Production create failed: ' . $e->getMessage());
                setFlash('error', 'Failed to create batch. Please try again.');
            }
            $this->redirect('production');
            return;
        }
        $this->render('form', [
            'rawMaterials' => (new RawMaterialModel())->all(),
            'packagingMaterials' => (new PackagingMaterialModel())->all(),
            'machines' => (new MachineModel())->all(),
            'users' => (new UserModel())->all(),
        ]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $batch = $this->model->find($id);
        if (!$batch) { setFlash('error', 'Not found.'); $this->redirect('production'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newStatus = $this->getInput('status');
            $this->model->update($id, [
                'Flavour' => $this->getInput('flavour'),
                'Quantity' => (float)$this->getInput('quantity', '0'),
                'Status' => $newStatus,
                'RawMaterialID' => $this->getInput('raw_material_id') ?: null,
                'PackagingMaterialID' => $this->getInput('packaging_material_id') ?: null,
                'MachineID' => $this->getInput('machine_id') ?: null,
                'Notes' => $this->getInput('notes'),
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Production', $id, 'Updated batch');
            setFlash('success', 'Batch updated.');
            $this->redirect('production');
            return;
        }
        $this->render('form', [
            'batch' => $batch,
            'rawMaterials' => (new RawMaterialModel())->all(),
            'packagingMaterials' => (new PackagingMaterialModel())->all(),
            'machines' => (new MachineModel())->all(),
            'users' => (new UserModel())->all(),
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
