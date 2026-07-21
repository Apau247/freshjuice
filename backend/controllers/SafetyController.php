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
            $this->render('inspection_form');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('SAF');
        $this->model->create([
            'SafetyID' => $id,
            'InspectionDate' => $this->getInput('InspectionDate'),
            'InspectionType' => sanitize($this->getInput('InspectionType', 'General')),
            'Area' => sanitize($this->getInput('Area')),
            'InspectorID' => $_SESSION['user_id'] ?? null,
            'HazardLevel' => sanitize($this->getInput('HazardLevel', 'Low')),
            'Findings' => sanitize($this->getInput('Findings')),
            'CorrectiveAction' => sanitize($this->getInput('CorrectiveAction')),
            'ResponsiblePerson' => sanitize($this->getInput('ResponsiblePerson')),
            'TargetDate' => $this->getInput('TargetDate'),
            'Status' => sanitize($this->getInput('Status', 'Open')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'safety', $id, 'Created safety inspection');
        setFlash('success', 'Safety inspection created successfully.');
        $this->redirect('safety');
    }

    public function edit()
    {
        $id = $this->getInput('id');
        $inspection = $this->model->find($id);

        if (!$inspection) {
            setFlash('error', 'Safety inspection not found.');
            $this->redirect('safety');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('inspection_form', ['inspection' => $inspection]);
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $this->model->update($id, [
            'InspectionDate' => $this->getInput('InspectionDate'),
            'InspectionType' => sanitize($this->getInput('InspectionType', 'General')),
            'Area' => sanitize($this->getInput('Area')),
            'HazardLevel' => sanitize($this->getInput('HazardLevel', 'Low')),
            'Findings' => sanitize($this->getInput('Findings')),
            'CorrectiveAction' => sanitize($this->getInput('CorrectiveAction')),
            'ResponsiblePerson' => sanitize($this->getInput('ResponsiblePerson')),
            'TargetDate' => $this->getInput('TargetDate'),
            'Status' => sanitize($this->getInput('Status')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'safety', $id, 'Updated safety inspection');
        setFlash('success', 'Safety inspection updated successfully.');
        $this->redirect('safety');
    }

    public function delete()
    {
        $id = $this->getInput('id');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'safety', $id, 'Deleted safety inspection');
        setFlash('success', 'Safety inspection deleted successfully.');
        $this->redirect('safety');
    }
}
