<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/QualityInspectionModel.php';
require_once __DIR__ . '/../models/ProductionBatchModel.php';
require_once __DIR__ . '/../models/FinishedGoodModel.php';

class QualityController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new QualityInspectionModel();
        $this->viewPath = 'quality';
    }

    public function index(): void {
        $this->render('index', ['inspections' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->getInput('InspectionID') ?: generateId('QI');
            $result = $this->getInput('result', 'Pending');
            $batchId = $this->getInput('batch_id');

            $this->model->create([
                'InspectionID' => $id,
                'InspectionType' => $this->getInput('inspection_type'),
                'BatchID' => $batchId ?: null,
                'InspectionDate' => $this->getInput('inspection_date'),
                'Result' => $result,
                'DefectsFound' => $this->getInput('defects_found'),
                'TestResults' => $this->getInput('test_results'),
                'CAPA' => $this->getInput('capa'),
                'InspectorID' => $_SESSION['user_id'] ?? null,
                'Status' => $result === 'Pass' ? 'Closed' : 'Open',
            ]);

            if ($result === 'Pass' && $batchId) {
                $this->createFinishedGoods($batchId);
            }

            logAudit($_SESSION['user_id'], 'CREATE', 'Quality', $id, "Inspection: $result");
            setFlash('success', 'Inspection recorded.');
            $this->redirect('quality');
            return;
        }
        $batches = (new ProductionBatchModel())->query(
            "SELECT * FROM production_batches WHERE Status IN ('Pending','In Progress') ORDER BY BatchNumber"
        );
        $this->render('form', ['batches' => $batches]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('quality'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->getInput('result');
            $this->model->update($id, [
                'InspectionType' => $this->getInput('inspection_type'),
                'Result' => $result, 'DefectsFound' => $this->getInput('defects_found'),
                'TestResults' => $this->getInput('test_results'),
                'CAPA' => $this->getInput('capa'),
                'Status' => $result === 'Pass' ? 'Closed' : 'Open',
            ]);

            if ($result === 'Pass' && $item['BatchID']) {
                $this->createFinishedGoods($item['BatchID']);
            }

            logAudit($_SESSION['user_id'], 'UPDATE', 'Quality', $id, "Updated inspection: $result");
            setFlash('success', 'Inspection updated.');
            $this->redirect('quality');
            return;
        }
        $batches = (new ProductionBatchModel())->all();
        $this->render('form', ['inspection' => $item, 'batches' => $batches]);
    }

    public function delete(): void {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Quality', $id, 'Deleted inspection');
        setFlash('success', 'Inspection deleted.');
        $this->redirect('quality');
    }

    private function createFinishedGoods(string $batchId): void {
        $batchModel = new ProductionBatchModel();
        $batch = $batchModel->find($batchId);
        if (!$batch || $batch['Status'] === 'Completed') return;

        $fgModel = new FinishedGoodModel();
        $existing = $fgModel->queryOne(
            "SELECT * FROM finished_goods WHERE BatchID = ?", [$batchId]
        );
        if ($existing) return;

        $batchModel->update($batchId, ['Status' => 'Completed']);
        $fgId = generateId('FG');
        $expiry = date('Y-m-d', strtotime('+6 months', strtotime($batch['ProductionDate'])));
        $fgModel->create([
            'FG_ID' => $fgId, 'BatchID' => $batchId,
            'Flavour' => $batch['Flavour'], 'ExpiryDate' => $expiry,
            'QuantityAvailable' => $batch['Quantity'],
            'Unit' => $batch['Unit'] ?? 'bottles',
        ]);
        logAudit($_SESSION['user_id'], 'CREATE', 'Finished Goods', $fgId, "Auto-created from batch $batchId");
    }
}
