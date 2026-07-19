<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/EfficiencyModel.php';
require_once __DIR__ . '/../models/MachineModel.php';

class EfficiencyController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new EfficiencyModel();
        $this->viewPath = 'efficiency';
    }

    public function index(): void
    {
        $data = [
            'efficiencies' => $this->model->getAllDetailed(),
            'monthlyOEE' => $this->model->getMonthlyOEE(),
        ];
        $this->render('index', $data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $plannedRunTime = (float)$this->getInput('planned_run_time');
            $actualRunTime = (float)$this->getInput('actual_run_time');
            $totalProduced = (int)$this->getInput('total_produced');
            $goodProduced = (int)$this->getInput('good_produced');
            $defectCount = $totalProduced - $goodProduced;

            $availabilityRate = $plannedRunTime > 0
                ? round(($actualRunTime / $plannedRunTime) * 100, 2) : 0;
            $performanceRate = $totalProduced > 0
                ? round(($goodProduced / $totalProduced) * 100, 2) : 0;
            $qualityRate = $totalProduced > 0
                ? round(($goodProduced / $totalProduced) * 100, 2) : 0;
            $oee = round(($availabilityRate * $performanceRate * $qualityRate) / 10000, 2);

            $id = generateId('EFF');
            $this->model->create([
                'EfficiencyID' => $id,
                'Date' => $this->getInput('record_date', date('Y-m-d')),
                'Shift' => $this->getInput('shift'),
                'MachineID' => $this->getInput('machine_id'),
                'PlannedRunTime' => $plannedRunTime,
                'ActualRunTime' => $actualRunTime,
                'DowntimeMinutes' => (float)$this->getInput('downtime_minutes', '0'),
                'TotalProduced' => $totalProduced,
                'GoodProduced' => $goodProduced,
                'DefectCount' => $defectCount,
                'AvailabilityRate' => $availabilityRate,
                'PerformanceRate' => $performanceRate,
                'QualityRate' => $qualityRate,
                'OEE' => $oee,
                'Notes' => $this->getInput('notes'),
                'recordedBy' => $_SESSION['user_id'] ?? null,
            ]);

            logAudit($_SESSION['user_id'], 'CREATE', 'Efficiency', $id, "Created OEE record: {$oee}%");
            setFlash('success', "Efficiency record created. OEE: {$oee}%");
            $this->redirect('efficiency');
            return;
        }
        $this->render('form', ['machines' => (new MachineModel())->all()]);
    }

    public function edit(): void
    {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('efficiency'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $plannedRunTime = (float)$this->getInput('planned_run_time');
            $actualRunTime = (float)$this->getInput('actual_run_time');
            $totalProduced = (int)$this->getInput('total_produced');
            $goodProduced = (int)$this->getInput('good_produced');
            $defectCount = $totalProduced - $goodProduced;

            $availabilityRate = $plannedRunTime > 0
                ? round(($actualRunTime / $plannedRunTime) * 100, 2) : 0;
            $performanceRate = $totalProduced > 0
                ? round(($goodProduced / $totalProduced) * 100, 2) : 0;
            $qualityRate = $totalProduced > 0
                ? round(($goodProduced / $totalProduced) * 100, 2) : 0;
            $oee = round(($availabilityRate * $performanceRate * $qualityRate) / 10000, 2);

            $this->model->update($id, [
                'Date' => $this->getInput('record_date'),
                'Shift' => $this->getInput('shift'),
                'MachineID' => $this->getInput('machine_id'),
                'PlannedRunTime' => $plannedRunTime,
                'ActualRunTime' => $actualRunTime,
                'DowntimeMinutes' => (float)$this->getInput('downtime_minutes', '0'),
                'TotalProduced' => $totalProduced,
                'GoodProduced' => $goodProduced,
                'DefectCount' => $defectCount,
                'AvailabilityRate' => $availabilityRate,
                'PerformanceRate' => $performanceRate,
                'QualityRate' => $qualityRate,
                'OEE' => $oee,
                'Notes' => $this->getInput('notes'),
            ]);

            logAudit($_SESSION['user_id'], 'UPDATE', 'Efficiency', $id, "Updated OEE: {$oee}%");
            setFlash('success', "Efficiency record updated. OEE: {$oee}%");
            $this->redirect('efficiency');
            return;
        }
        $this->render('form', ['efficiency' => $item, 'machines' => (new MachineModel())->all()]);
    }

    public function delete(): void
    {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Efficiency', $id, 'Deleted efficiency record');
        setFlash('success', 'Efficiency record deleted.');
        $this->redirect('efficiency');
    }
}
