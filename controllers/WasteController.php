<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/WasteRecordModel.php';
require_once __DIR__ . '/../models/ProductionBatchModel.php';

class WasteController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new WasteRecordModel();
        $this->viewPath = 'waste';
    }

    public function index(): void {
        $this->render('index', ['records' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->create([
                'WasteID' => $this->getInput('WasteID'),
                'Date' => $this->getInput('date'),
                'WasteType' => $this->getInput('waste_type', 'Production'),
                'Quantity' => (float)$this->getInput('quantity', '0'),
                'Unit' => $this->getInput('unit', 'kg'),
                'DisposalMethod' => $this->getInput('disposal_method', 'Landfill'),
                'BatchID' => $this->getInput('batch_id') ?: null,
                'EnvironmentalImpact' => $this->getInput('environmental_impact'),
                'RecordedBy' => $_SESSION['user_id'] ?? null,
            ]);
            setFlash('success', 'Waste record created.');
            $this->redirect('waste');
            return;
        }
        $this->render('form', ['batches' => (new ProductionBatchModel())->all()]);
    }

    public function delete(): void {
        $this->model->delete($this->getInput('id'));
        setFlash('success', 'Record deleted.');
        $this->redirect('waste');
    }
}
