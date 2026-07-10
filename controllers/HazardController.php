<?php
class HazardController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new HazardModel();
        $this->viewPath = 'safety';
    }

    public function index()
    {
        $data = [
            'hazards' => $this->model->getAllActive(),
            'highRisk' => $this->model->getHighRisk(),
        ];
        $this->render('hazard_index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('safety/hazards');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('HAZ');
        $this->model->create([
            'HazardID' => $id,
            'HazardDescription' => sanitize($this->getInput('HazardDescription')),
            'RiskLevel' => sanitize($this->getInput('RiskCategory')),
            'Location' => sanitize($this->getInput('Location')),
            'IdentifiedDate' => $this->getInput('IdentifiedDate'),
            'Status' => sanitize($this->getInput('Status')),
            'MitigationPlan' => sanitize($this->getInput('ControlMeasures')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'hazard', $id, 'Created hazard record');
        setFlash('success', 'Hazard record created successfully.');
        $this->redirect('safety/hazards');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('safety/hazards');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');
        $id = $this->getInput('HazardID');

        $this->model->update($id, [
            'HazardDescription' => sanitize($this->getInput('HazardDescription')),
            'RiskLevel' => sanitize($this->getInput('RiskCategory')),
            'Location' => sanitize($this->getInput('Location')),
            'IdentifiedDate' => $this->getInput('IdentifiedDate'),
            'Status' => sanitize($this->getInput('Status')),
            'MitigationPlan' => sanitize($this->getInput('ControlMeasures')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'hazard', $id, 'Updated hazard record');
        setFlash('success', 'Hazard record updated successfully.');
        $this->redirect('safety/hazards');
    }

    public function delete()
    {
        $id = $this->getInput('HazardID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'hazard', $id, 'Deleted hazard record');
        setFlash('success', 'Hazard record deleted successfully.');
        $this->redirect('safety/hazards');
    }
}
