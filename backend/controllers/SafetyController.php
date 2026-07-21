<?php
class SafetyController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new SafetyModel();
        $this->viewPath = 'safety';
    }

    public function index()
    {
        $data = [
            'inspections' => $this->model->getAllDetailed(),
            'statusCounts' => $this->model->getStatusCounts(),
            'hazardCounts' => $this->model->getHazardCounts(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('safety');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('SAF');
        $this->model->create([
            'SafetyID' => $id,
            'InspectionDate' => $this->getInput('InspectionDate'),
            'InspectorName' => sanitize($this->getInput('InspectorName')),
            'Area' => sanitize($this->getInput('Area')),
            'Status' => sanitize($this->getInput('Status')),
            'Findings' => sanitize($this->getInput('Findings')),
            'CorrectiveActions' => sanitize($this->getInput('CorrectiveActions')),
            'NextInspectionDate' => $this->getInput('NextInspectionDate'),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'safety', $id, 'Created safety inspection');
        setFlash('success', 'Safety inspection created successfully.');
        $this->redirect('safety');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('safety');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');
        $id = $this->getInput('SafetyID');

        $this->model->update($id, [
            'InspectionDate' => $this->getInput('InspectionDate'),
            'InspectorName' => sanitize($this->getInput('InspectorName')),
            'Area' => sanitize($this->getInput('Area')),
            'Status' => sanitize($this->getInput('Status')),
            'Findings' => sanitize($this->getInput('Findings')),
            'CorrectiveActions' => sanitize($this->getInput('CorrectiveActions')),
            'NextInspectionDate' => $this->getInput('NextInspectionDate'),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'safety', $id, 'Updated safety inspection');
        setFlash('success', 'Safety inspection updated successfully.');
        $this->redirect('safety');
    }

    public function delete()
    {
        $id = $this->getInput('SafetyID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'safety', $id, 'Deleted safety inspection');
        setFlash('success', 'Safety inspection deleted successfully.');
        $this->redirect('safety');
    }
}
