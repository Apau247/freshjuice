<?php
class PermitController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new PermitModel();
        $this->viewPath = 'permits';
    }

    public function index()
    {
        $data = [
            'permits' => $this->model->all(),
            'expiringSoon' => $this->model->getExpiringSoon(),
        ];
        $this->render('index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('permits');
            return;
        }

        $this->requireRole('admin', 'manager');

        $id = generateId('PRM');
        $this->model->create([
            'PermitID' => $id,
            'PermitType' => sanitize($this->getInput('PermitType')),
            'PermitNumber' => sanitize($this->getInput('PermitNumber')),
            'IssuingAuthority' => sanitize($this->getInput('IssuingAuthority')),
            'IssueDate' => $this->getInput('IssueDate'),
            'ExpiryDate' => $this->getInput('ExpiryDate'),
            'Status' => sanitize($this->getInput('Status')),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'permit', $id, 'Created permit record');
        setFlash('success', 'Permit created successfully.');
        $this->redirect('permits');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('permits');
            return;
        }

        $this->requireRole('admin', 'manager');
        $id = $this->getInput('PermitID');

        $this->model->update($id, [
            'PermitType' => sanitize($this->getInput('PermitType')),
            'PermitNumber' => sanitize($this->getInput('PermitNumber')),
            'IssuingAuthority' => sanitize($this->getInput('IssuingAuthority')),
            'IssueDate' => $this->getInput('IssueDate'),
            'ExpiryDate' => $this->getInput('ExpiryDate'),
            'Status' => sanitize($this->getInput('Status')),
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'permit', $id, 'Updated permit record');
        setFlash('success', 'Permit updated successfully.');
        $this->redirect('permits');
    }

    public function delete()
    {
        $id = $this->getInput('PermitID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'permit', $id, 'Deleted permit record');
        setFlash('success', 'Permit deleted successfully.');
        $this->redirect('permits');
    }
}
