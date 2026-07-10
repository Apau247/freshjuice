<?php
class ImprovementController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ImprovementModel();
        $this->viewPath = 'improvement';
    }

    public function index()
    {
        $data = [
            'initiatives' => $this->model->getAllDetailed(),
            'statusCounts' => $this->model->getStatusCounts(),
            'overdue' => $this->model->getOverdue(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('improvement');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('IMP');
        $this->model->create([
            'ImprovementID' => $id,
            'Title' => sanitize($this->getInput('Title')),
            'Description' => sanitize($this->getInput('Description')),
            'Category' => sanitize($this->getInput('Category')),
            'Priority' => sanitize($this->getInput('Status')),
            'AssignedTo' => sanitize($this->getInput('ResponsiblePerson')),
            'TargetDate' => $this->getInput('TargetDate'),
            'Status' => sanitize($this->getInput('Status')),
            'Progress' => $this->getInput('Effectiveness'),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'improvement', $id, 'Created improvement record');
        setFlash('success', 'Improvement record created successfully.');
        $this->redirect('improvement');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('improvement');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');
        $id = $this->getInput('ImprovementID');

        $this->model->update($id, [
            'Title' => sanitize($this->getInput('Title')),
            'Description' => sanitize($this->getInput('Description')),
            'Category' => sanitize($this->getInput('Category')),
            'Priority' => sanitize($this->getInput('Status')),
            'AssignedTo' => sanitize($this->getInput('ResponsiblePerson')),
            'TargetDate' => $this->getInput('TargetDate'),
            'Status' => sanitize($this->getInput('Status')),
            'Progress' => $this->getInput('Effectiveness'),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'improvement', $id, 'Updated improvement record');
        setFlash('success', 'Improvement record updated successfully.');
        $this->redirect('improvement');
    }

    public function delete()
    {
        $id = $this->getInput('ImprovementID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'improvement', $id, 'Deleted improvement record');
        setFlash('success', 'Improvement record deleted successfully.');
        $this->redirect('improvement');
    }
}
