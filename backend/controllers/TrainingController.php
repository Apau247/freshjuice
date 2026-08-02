<?php
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/TrainingModel.php';
require_once __DIR__ . '/../models/StaffModel.php';

class TrainingController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new TrainingModel();
        $this->viewPath = 'training';
    }

    public function index()
    {
        $completionStats = $this->model->getCompletionStats();
        $data = [
            'trainings' => $this->model->getAllDetailed(),
            'completionStats' => $completionStats,
            'completedCount' => (int)($completionStats['completed'] ?? 0),
            'scheduledCount' => (int)($completionStats['scheduled'] ?? 0),
            'expiringCertCount' => count($this->model->getExpiringCerts()),
            'totalCount' => $this->model->count(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('form', ['staffList' => (new StaffModel())->all()]);
            return;
        }

        $this->requireCanCreate('training');

        $id = generateId('TRN');
        $this->model->create([
            'TrainingID' => $id,
            'StaffID' => sanitize($this->getInput('StaffID')),
            'TrainingType' => sanitize($this->getInput('TrainingType')),
            'TrainingDate' => $this->getInput('TrainingDate'),
            'Duration' => sanitize($this->getInput('Duration')),
            'Trainer' => sanitize($this->getInput('Trainer')),
            'ExpiryDate' => $this->getInput('ExpiryDate'),
            'Status' => sanitize($this->getInput('Status', 'Scheduled')),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'training', $id, 'Created training record');
        setFlash('success', 'Training record created successfully.');
        $this->redirect('training');
    }

    public function edit()
    {
        $id = $this->getInput('id');
        $training = $this->model->find($id);

        if (!$training) {
            setFlash('error', 'Training record not found.');
            $this->redirect('training');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('form', ['training' => $training, 'staffList' => (new StaffModel())->all()]);
            return;
        }

        $this->requireCanEdit('training');

        $this->model->update($id, [
            'StaffID' => sanitize($this->getInput('StaffID')),
            'TrainingType' => sanitize($this->getInput('TrainingType')),
            'TrainingDate' => $this->getInput('TrainingDate'),
            'Duration' => sanitize($this->getInput('Duration')),
            'Trainer' => sanitize($this->getInput('Trainer')),
            'ExpiryDate' => $this->getInput('ExpiryDate'),
            'Status' => sanitize($this->getInput('Status')),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'training', $id, 'Updated training record');
        setFlash('success', 'Training record updated successfully.');
        $this->redirect('training');
    }

    public function delete()
    {
        $id = $this->getInput('id');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'training', $id, 'Deleted training record');
        setFlash('success', 'Training record deleted successfully.');
        $this->redirect('training');
    }
}
