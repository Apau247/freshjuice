<?php
class PpeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new PpeModel();
        $this->viewPath = 'ppe';
    }

    public function index()
    {
        $data = [
            'records' => $this->model->getAllDetailed(),
            'expiringSoon' => $this->model->getExpiringSoon(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('ppe');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('PPE');
        $this->model->create([
            'PpeID' => $id,
            'EquipmentType' => sanitize($this->getInput('EquipmentType')),
            'EmployeeName' => sanitize($this->getInput('EmployeeName')),
            'IssueDate' => $this->getInput('IssueDate'),
            'ExpiryDate' => $this->getInput('ExpiryDate'),
            'Condition' => sanitize($this->getInput('Condition')),
            'Status' => sanitize($this->getInput('Status')),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'ppe', $id, 'Created PPE record');
        setFlash('success', 'PPE record created successfully.');
        $this->redirect('ppe');
    }

    public function delete()
    {
        $id = $this->getInput('PpeID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'ppe', $id, 'Deleted PPE record');
        setFlash('success', 'PPE record deleted successfully.');
        $this->redirect('ppe');
    }
}
