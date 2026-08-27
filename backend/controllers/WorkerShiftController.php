<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/WorkerShiftModel.php';
require_once __DIR__ . '/../models/WorkerModel.php';
require_once __DIR__ . '/../models/StaffModel.php';

class WorkerShiftController extends Controller
{
    private WorkerShiftModel $workerShifts;

    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'worker_shifts';
        $this->workerShifts = new WorkerShiftModel();
    }

    public function index(): void
    {
        $this->requireCan('workers');

        $date = $this->getInput('date') ?: date('Y-m-d');
        $from = $this->getInput('from') ?: date('Y-m-d', strtotime('-7 days', strtotime($date)));
        $to   = $this->getInput('to')   ?: date('Y-m-d', strtotime('+7 days', strtotime($date)));

        $assignments = $this->workerShifts->getByDateRange($from, $to);
        $todaySummary = $this->workerShifts->getTodaySummary();

        $workerModel = new WorkerModel();
        $staffModel  = new StaffModel();
        $workers     = $workerModel->all();
        $shifts      = $staffModel->getShifts();

        $this->render('index', [
            'assignments'   => $assignments,
            'todaySummary'  => $todaySummary,
            'workers'       => $workers,
            'shifts'        => $shifts,
            'date'          => $date,
            'from'          => $from,
            'to'            => $to,
        ]);
    }

    public function create(): void
    {
        $this->requireCanCreate('workers');
        $workerModel = new WorkerModel();
        $staffModel  = new StaffModel();

        $this->render('create', [
            'workers' => $workerModel->all(),
            'shifts'  => $staffModel->getShifts(),
        ]);
    }

    public function store(): void
    {
        $this->requireCanCreate('workers');
        $workerId = $this->getInput('WorkerID');
        $shiftId  = $this->getInput('ShiftID');
        $shiftDate = $this->getInput('ShiftDate');
        $status   = $this->getInput('Status') ?: 'Scheduled';

        if (!$workerId || !$shiftId || !$shiftDate) {
            setFlash('error', 'Worker, shift, and date are required.');
            $this->redirect('worker-shifts');
            return;
        }

        try {
            $this->workerShifts->create([
                'WorkerID'  => $workerId,
                'ShiftID'   => $shiftId,
                'ShiftDate' => $shiftDate,
                'Status'    => $status,
            ]);
            setFlash('success', 'Worker shift assignment created.');
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'Duplicate')) {
                setFlash('error', 'This worker is already assigned to a shift on this date.');
            } else {
                setFlash('error', 'Failed to create assignment: ' . $e->getMessage());
            }
        }
        $this->redirect('worker-shifts?date=' . $shiftDate);
    }

    public function edit(): void
    {
        $this->requireCanEdit('workers');
        $id = $this->getInput('id');
        $assignment = $this->workerShifts->find($id);
        if (!$assignment) {
            setFlash('error', 'Assignment not found.');
            $this->redirect('worker-shifts');
            return;
        }

        $workerModel = new WorkerModel();
        $staffModel  = new StaffModel();

        $this->render('edit', [
            'assignment' => $assignment,
            'workers'    => $workerModel->all(),
            'shifts'     => $staffModel->getShifts(),
        ]);
    }

    public function update(): void
    {
        $this->requireCanEdit('workers');
        $id = $this->getInput('id');
        $this->workerShifts->updateAssignment($id, [
            'WorkerID'  => $this->getInput('WorkerID'),
            'ShiftID'   => $this->getInput('ShiftID'),
            'ShiftDate' => $this->getInput('ShiftDate'),
            'Status'    => $this->getInput('Status'),
        ]);
        setFlash('success', 'Assignment updated.');
        $this->redirect('worker-shifts?date=' . ($this->getInput('ShiftDate') ?: date('Y-m-d')));
    }

    public function delete(): void
    {
        $this->requireCanEdit('workers');
        $id = $this->getInput('id');
        $this->workerShifts->deleteAssignment($id);
        setFlash('success', 'Assignment deleted.');
        $this->redirect('worker-shifts');
    }

    public function bulk(): void
    {
        $this->requireCanCreate('workers');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('worker-shifts');
            return;
        }

        $workerIds = $_POST['worker_ids'] ?? [];
        $shiftId   = $this->getInput('ShiftID');
        $shiftDate = $this->getInput('ShiftDate');

        if (empty($workerIds) || !$shiftId || !$shiftDate) {
            setFlash('error', 'Select workers, a shift, and a date.');
            $this->redirect('worker-shifts');
            return;
        }

        $created = 0;
        $skipped = 0;
        foreach ($workerIds as $wid) {
            try {
                $this->workerShifts->create([
                    'WorkerID'  => $wid,
                    'ShiftID'   => $shiftId,
                    'ShiftDate' => $shiftDate,
                    'Status'    => 'Scheduled',
                ]);
                $created++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        $msg = "{$created} assignment(s) created.";
        if ($skipped > 0) $msg .= " {$skipped} skipped (already assigned).";
        setFlash('success', $msg);
        $this->redirect('worker-shifts?date=' . $shiftDate);
    }
}
