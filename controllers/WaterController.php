<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/WaterModel.php';

class WaterController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new WaterModel();
        $this->viewPath = 'water';
    }

    public function index(): void {
        $this->render('index', [
            'usageRecords' => $this->model->getUsageRecords(),
            'qualityTests' => $this->model->getQualityTests(),
            'usageByType' => $this->model->getUsageByType(),
        ]);
    }

    public function createUsage(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->db->prepare(
                "INSERT INTO water_usage (WaterUsageID, Date, UsageType, Quantity, Unit, Purpose, RecordedBy) VALUES (?,?,?,?,?,?,?)"
            )->execute([
                $this->getInput('WaterUsageID'), $this->getInput('date'),
                $this->getInput('usage_type'), (float)$this->getInput('quantity', '0'),
                $this->getInput('unit', 'litres'), $this->getInput('purpose'),
                $_SESSION['user_id'] ?? null,
            ]);
            setFlash('success', 'Water usage recorded.');
            $this->redirect('water');
            return;
        }
        $this->render('usage_form');
    }

    public function createTest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->db->prepare(
                "INSERT INTO water_quality_tests (WaterTestID, TestDate, TestType, pH_Level, Turbidity, TDS, Chlorine, BacteriaCount, Result, Notes, TestedBy) VALUES (?,?,?,?,?,?,?,?,?,?,?)"
            )->execute([
                $this->getInput('WaterTestID'), $this->getInput('test_date'),
                $this->getInput('test_type'),
                $this->getInput('ph_level') ?: null, $this->getInput('turbidity') ?: null,
                $this->getInput('tds') ?: null, $this->getInput('chlorine') ?: null,
                $this->getInput('bacteria_count') ?: null,
                $this->getInput('result', 'Pending'), $this->getInput('notes'),
                $this->getInput('tested_by') ?: ($_SESSION['user_id'] ?? null),
            ]);
            setFlash('success', 'Water quality test recorded.');
            $this->redirect('water');
            return;
        }
        $this->render('test_form');
    }
}
