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
                $this->getInput('PowerUsageID'), $this->getInput('Date'),
                $this->getInput('Source', 'Grid'),
                (float)$this->getInput('ConsumptionKWh', '0'),
                (float)$this->getInput('Cost', '0'), $this->getInput('Notes'),
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
                $this->getInput('LogID'), $this->getInput('Date'),
                $this->getInput('StartTime'), $this->getInput('EndTime'),
                (float)$this->getInput('RuntimeHrs', '0'),
                (float)$this->getInput('FuelUsed', '0'),
                $this->getInput('FuelUnit', 'litres'),
                $this->getInput('Reason'), $this->getInput('Notes'),
            ]);
            setFlash('success', 'Generator log recorded.');
            $this->redirect('power');
            return;
        }
        $this->render('generator_form');
    }
}
