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
            'ppeItems' => $this->model->getAllDetailed(),
            'expiringSoon' => $this->model->getExpiringSoon(),
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

        $id = generateId('PPE');
        $this->model->create([
            'PPE_ID' => $id,
            'StaffID' => sanitize($this->getInput('StaffID')),
            'PPESource' => sanitize($this->getInput('PPESource')),
            'DateIssued' => $this->getInput('DateIssued'),
            'ExpiryDate' => $this->getInput('ExpiryDate'),
            '`Condition`' => sanitize($this->getInput('Condition', 'New')),
            'ReplacementNeeded' => $this->getInput('ReplacementNeeded') ? 1 : 0,
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'ppe', $id, 'Created PPE record');
        setFlash('success', 'PPE record created successfully.');
        $this->redirect('ppe');
    }

    public function edit()
    {
        $id = $this->getInput('id');
        $ppe = $this->model->find($id);

        if (!$ppe) {
            setFlash('error', 'PPE record not found.');
            $this->redirect('ppe');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('form', ['ppe' => $ppe, 'staffList' => (new StaffModel())->all()]);
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $this->model->update($id, [
            'StaffID' => sanitize($this->getInput('StaffID')),
            'PPESource' => sanitize($this->getInput('PPESource')),
            'DateIssued' => $this->getInput('DateIssued'),
            'ExpiryDate' => $this->getInput('ExpiryDate'),
            '`Condition`' => sanitize($this->getInput('Condition', 'New')),
            'ReplacementNeeded' => $this->getInput('ReplacementNeeded') ? 1 : 0,
            'Notes' => sanitize($this->getInput('Notes')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'ppe', $id, 'Updated PPE record');
        setFlash('success', 'PPE record updated successfully.');
        $this->redirect('ppe');
    }

    public function delete()
    {
        $id = $this->getInput('id');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'ppe', $id, 'Deleted PPE record');
        setFlash('success', 'PPE record deleted successfully.');
        $this->redirect('ppe');
    }
}
