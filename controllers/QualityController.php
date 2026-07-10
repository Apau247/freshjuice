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
            $id = $this->getInput('InspectionID');
            $result = $this->getInput('Result', 'Pending');
            $batchId = $this->getInput('BatchID');

            $this->model->create([
                'InspectionID' => $id,
                'InspectionType' => $this->getInput('InspectionType'),
                'BatchID' => $batchId ?: null,
                'InspectionDate' => $this->getInput('InspectionDate'),
                'Result' => $result,
                'DefectsFound' => $this->getInput('DefectsFound'),
                'TestResults' => $this->getInput('TestResults'),
                'CAPA' => $this->getInput('CAPA'),
                'InspectorID' => $_SESSION['user_id'] ?? null,
                'Status' => $result === 'Pass' ? 'Closed' : 'Open',
            ]);

            if ($result === 'Pass' && $batchId) {
                $batchModel = new ProductionBatchModel();
                $batch = $batchModel->find($batchId);
                if ($batch && $batch['Status'] !== 'Completed') {
                    $batchModel->update($batchId, ['Status' => 'Completed']);
                    $fgModel = new FinishedGoodModel();
                    $fgId = generateId('FG');
                    $expiry = date('Y-m-d', strtotime('+6 months', strtotime($batch['ProductionDate'])));
                    $fgModel->create([
                        'FG_ID' => $fgId, 'BatchID' => $batchId,
                        'Flavour' => $batch['Flavour'], 'ExpiryDate' => $expiry,
                        'QuantityAvailable' => $batch['Quantity'],
                        'Unit' => 'bottles',
                    ]);
                    logAudit($_SESSION['user_id'], 'CREATE', 'Finished Goods', $fgId, "Auto-created from batch $batchId");
                }
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
            $result = $this->getInput('Result');
            $this->model->update($id, [
                'InspectionType' => $this->getInput('InspectionType'),
                'Result' => $result, 'DefectsFound' => $this->getInput('DefectsFound'),
                'TestResults' => $this->getInput('TestResults'),
                'CAPA' => $this->getInput('CAPA'),
                'Status' => $result === 'Pass' ? 'Closed' : 'Open',
            ]);

            if ($result === 'Pass' && $item['BatchID']) {
                $batchModel = new ProductionBatchModel();
                $batch = $batchModel->find($item['BatchID']);
                if ($batch && $batch['Status'] !== 'Completed') {
                    $batchModel->update($item['BatchID'], ['Status' => 'Completed']);
                    $fgModel = new FinishedGoodModel();
                    $fgId = generateId('FG');
                    $expiry = date('Y-m-d', strtotime('+6 months', strtotime($batch['ProductionDate'])));
                    $fgModel->create([
                        'FG_ID' => $fgId, 'BatchID' => $item['BatchID'],
                        'Flavour' => $batch['Flavour'], 'ExpiryDate' => $expiry,
                        'QuantityAvailable' => $batch['Quantity'], 'Unit' => 'bottles',
                    ]);
                }
            }

            setFlash('success', 'Inspection updated.');
            $this->redirect('quality');
            return;
        }
        $batches = (new ProductionBatchModel())->all();
        $this->render('form', ['inspection' => $item, 'batches' => $batches]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Inspection deleted.');
        $this->redirect('quality');
    }
}
