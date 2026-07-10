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
            'records' => $this->model->getAllDetailed(),
            'completionStats' => $this->model->getCompletionStats(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('training');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('TRN');
        $this->model->create([
            'TrainingID' => $id,
            'EmployeeName' => sanitize($this->getInput('EmployeeName')),
            'TrainingType' => sanitize($this->getInput('TrainingType')),
            'TrainingDate' => $this->getInput('TrainingDate'),
            'CompletionStatus' => sanitize($this->getInput('CompletionStatus')),
            'Score' => $this->getInput('Score'),
            'CertificationExpiry' => $this->getInput('CertificationExpiry'),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'training', $id, 'Created training record');
        setFlash('success', 'Training record created successfully.');
        $this->redirect('training');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('training');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');
        $id = $this->getInput('TrainingID');

        $this->model->update($id, [
            'EmployeeName' => sanitize($this->getInput('EmployeeName')),
            'TrainingType' => sanitize($this->getInput('TrainingType')),
            'TrainingDate' => $this->getInput('TrainingDate'),
            'CompletionStatus' => sanitize($this->getInput('CompletionStatus')),
            'Score' => $this->getInput('Score'),
            'CertificationExpiry' => $this->getInput('CertificationExpiry'),
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
