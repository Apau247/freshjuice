<?php
class EfficiencyController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new EfficiencyModel();
        $this->viewPath = 'efficiency';
    }

    public function index()
    {
        $data = [
            'records' => $this->model->getAllDetailed(),
            'monthlyOEE' => $this->model->getMonthlyOEE(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('efficiency');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $plannedRunTime = (float) $this->getInput('PlannedRunTime');
        $actualRunTime = (float) $this->getInput('ActualRunTime');
        $idealRunRate = (float) $this->getInput('IdealRunRate');
        $totalProduced = (int) $this->getInput('TotalProduced');
        $goodProduced = (int) $this->getInput('GoodProduced');

        $availabilityRate = $plannedRunTime > 0
            ? round(($actualRunTime / $plannedRunTime) * 100, 2)
            : 0;
        $performanceRate = ($actualRunTime * $idealRunRate) > 0
            ? round(($totalProduced / ($actualRunTime * $idealRunRate)) * 100, 2)
            : 0;
        $qualityRate = $totalProduced > 0
            ? round(($goodProduced / $totalProduced) * 100, 2)
            : 0;
        $oee = round(($availabilityRate * $performanceRate * $qualityRate) / 10000, 2);

        $id = generateId('EFF');
        $this->model->create([
            'EfficiencyID' => $id,
            'MachineID' => sanitize($this->getInput('MachineID')),
            'RecordDate' => $this->getInput('RecordDate'),
            'PlannedRunTime' => $plannedRunTime,
            'ActualRunTime' => $actualRunTime,
            'IdealRunRate' => $idealRunRate,
            'TotalProduced' => $totalProduced,
            'GoodProduced' => $goodProduced,
            'AvailabilityRate' => $availabilityRate,
            'PerformanceRate' => $performanceRate,
            'QualityRate' => $qualityRate,
            'OEE' => $oee,
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'efficiency', $id, 'Created efficiency record with OEE: ' . $oee);
        setFlash('success', 'Efficiency record created. OEE: ' . $oee . '%');
        $this->redirect('efficiency');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('efficiency');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');
        $id = $this->getInput('EfficiencyID');

        $plannedRunTime = (float) $this->getInput('PlannedRunTime');
        $actualRunTime = (float) $this->getInput('ActualRunTime');
        $idealRunRate = (float) $this->getInput('IdealRunRate');
        $totalProduced = (int) $this->getInput('TotalProduced');
        $goodProduced = (int) $this->getInput('GoodProduced');

        $availabilityRate = $plannedRunTime > 0
            ? round(($actualRunTime / $plannedRunTime) * 100, 2)
            : 0;
        $performanceRate = ($actualRunTime * $idealRunRate) > 0
            ? round(($totalProduced / ($actualRunTime * $idealRunRate)) * 100, 2)
            : 0;
        $qualityRate = $totalProduced > 0
            ? round(($goodProduced / $totalProduced) * 100, 2)
            : 0;
        $oee = round(($availabilityRate * $performanceRate * $qualityRate) / 10000, 2);

        $this->model->update($id, [
            'MachineID' => sanitize($this->getInput('MachineID')),
            'RecordDate' => $this->getInput('RecordDate'),
            'PlannedRunTime' => $plannedRunTime,
            'ActualRunTime' => $actualRunTime,
            'IdealRunRate' => $idealRunRate,
            'TotalProduced' => $totalProduced,
            'GoodProduced' => $goodProduced,
            'AvailabilityRate' => $availabilityRate,
            'PerformanceRate' => $performanceRate,
            'QualityRate' => $qualityRate,
            'OEE' => $oee,
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'efficiency', $id, 'Updated efficiency record with OEE: ' . $oee);
        setFlash('success', 'Efficiency record updated. OEE: ' . $oee . '%');
        $this->redirect('efficiency');
    }

    public function delete()
    {
        $id = $this->getInput('EfficiencyID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'efficiency', $id, 'Deleted efficiency record');
        setFlash('success', 'Efficiency record deleted successfully.');
        $this->redirect('efficiency');
    }
}
