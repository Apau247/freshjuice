<?php
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
        $data = [
            'trainings' => $this->model->getAllDetailed(),
            'completionStats' => $this->model->getCompletionStats(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('form', ['staffList' => (new StaffModel())->all()]);
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('TRN');
        $this->model->create([
            'TrainingID' => $id,
            'EmployeeName' => sanitize($this->getInput('StaffID')),
            'TrainingType' => sanitize($this->getInput('TrainingType')),
            'TrainingDate' => $this->getInput('TrainingDate'),
            'CompletionStatus' => sanitize($this->getInput('Status')),
            'Score' => $this->getInput('Duration'),
            'CertificationExpiry' => $this->getInput('ExpiryDate'),
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

        $this->requireRole('admin', 'manager', 'supervisor');

        $this->model->update($id, [
            'EmployeeName' => sanitize($this->getInput('StaffID')),
            'TrainingType' => sanitize($this->getInput('TrainingType')),
            'TrainingDate' => $this->getInput('TrainingDate'),
            'CompletionStatus' => sanitize($this->getInput('Status')),
            'Score' => $this->getInput('Duration'),
            'CertificationExpiry' => $this->getInput('ExpiryDate'),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'training', $id, 'Updated training record');
        setFlash('success', 'Training record updated successfully.');
        $this->redirect('training');
    }

    public function delete()
    {
        $id = $this->getInput('TrainingID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'training', $id, 'Deleted training record');
        setFlash('success', 'Training record deleted successfully.');
        $this->redirect('training');
    }
}
