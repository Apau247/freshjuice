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
            $id = generateId('PU');
            $this->model->getDb()->prepare(
                "INSERT INTO power_usage (PowerUsageID, Date, Source, ConsumptionKWh, Cost, Notes) VALUES (?,?,?,?,?,?)"
            )->execute([
                $id, $this->getInput('date'),
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

    public function editUsage(): void {
        $id = $this->getInput('id');
        $record = $this->model->findUsage($id);
        if (!$record) { setFlash('error', 'Not found.'); $this->redirect('power'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateUsage($id, [
                'Date' => $this->getInput('date'),
                'Source' => $this->getInput('source', 'Grid'),
                'ConsumptionKWh' => (float)$this->getInput('consumption_kwh', '0'),
                'Cost' => (float)$this->getInput('cost', '0'),
                'Notes' => $this->getInput('notes'),
            ]);
            setFlash('success', 'Power usage updated.');
            $this->redirect('power');
            return;
        }
        $this->render('usage_form', ['record' => $record]);
    }

    public function deleteUsage(): void {
        $this->model->deleteUsage($this->getInput('id'));
        setFlash('success', 'Power usage deleted.');
        $this->redirect('power');
    }

    public function createGeneratorLog(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = generateId('GEN');
            $this->model->getDb()->prepare(
                "INSERT INTO generator_log (LogID, Date, StartTime, EndTime, RuntimeHrs, FuelUsed, FuelUnit, Reason, Notes) VALUES (?,?,?,?,?,?,?,?,?)"
            )->execute([
                $id, $this->getInput('date'),
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

    public function editGeneratorLog(): void {
        $id = $this->getInput('id');
        $log = $this->model->findGeneratorLog($id);
        if (!$log) { setFlash('error', 'Not found.'); $this->redirect('power'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateGeneratorLog($id, [
                'Date' => $this->getInput('date'),
                'StartTime' => $this->getInput('start_time'),
                'EndTime' => $this->getInput('end_time'),
                'RuntimeHrs' => (float)$this->getInput('runtime_hrs', '0'),
                'FuelUsed' => (float)$this->getInput('fuel_used', '0'),
                'FuelUnit' => $this->getInput('fuel_unit', 'litres'),
                'Reason' => $this->getInput('reason'),
                'Notes' => $this->getInput('notes'),
            ]);
            setFlash('success', 'Generator log updated.');
            $this->redirect('power');
            return;
        }
        $this->render('generator_form', ['record' => $log]);
    }

    public function deleteGeneratorLog(): void {
        $this->model->deleteGeneratorLog($this->getInput('id'));
        setFlash('success', 'Generator log deleted.');
        $this->redirect('power');
    }
}
