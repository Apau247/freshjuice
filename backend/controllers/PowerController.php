<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/PowerModel.php';

class PowerController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new PowerModel();
        $this->viewPath = 'power';
    }

    public function index(): void {
        $this->render('index', [
            'usageRecords' => $this->model->getUsageRecords(),
            'generatorLogs' => $this->model->getGeneratorLogs(),
        ]);
    }

    public function createUsage(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->db->prepare(
                "INSERT INTO power_usage (PowerUsageID, Date, Source, ConsumptionKWh, Cost, Notes) VALUES (?,?,?,?,?,?)"
            )->execute([
                $this->getInput('PowerUsageID'), $this->getInput('date'),
                $this->getInput('source', 'Grid'),
                (float)$this->getInput('consumption_kwh', '0'),
                (float)$this->getInput('cost', '0'), $this->getInput('notes'),
            ]);
            setFlash('success', 'Power usage recorded.');
            $this->redirect('power');
            return;
        }
        $this->render('usage_form');
    }

    public function createGeneratorLog(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->db->prepare(
                "INSERT INTO generator_log (LogID, Date, StartTime, EndTime, RuntimeHrs, FuelUsed, FuelUnit, Reason, Notes) VALUES (?,?,?,?,?,?,?,?,?)"
            )->execute([
                $this->getInput('LogID'), $this->getInput('date'),
                $this->getInput('start_time'), $this->getInput('end_time'),
                (float)$this->getInput('runtime_hrs', '0'),
                (float)$this->getInput('fuel_used', '0'),
                $this->getInput('fuel_unit', 'litres'),
                $this->getInput('reason'), $this->getInput('notes'),
            ]);
            setFlash('success', 'Generator log recorded.');
            $this->redirect('power');
            return;
        }
        $this->render('generator_form');
    }
}
