<?php
class AccidentController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new AccidentModel();
        $this->viewPath = 'safety';
    }

    public function index()
    {
        $data = [
            'accidents' => $this->model->getAllDetailed(),
        ];
        $this->render('accident_index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('safety/accidents');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('ACC');
        $this->model->create([
            'AccidentID' => $id,
            'AccidentDate' => $this->getInput('AccidentDate'),
            'Location' => sanitize($this->getInput('Location')),
            'Description' => sanitize($this->getInput('Description')),
            'InjurySeverity' => sanitize($this->getInput('InjurySeverity')),
            'InjuredPerson' => sanitize($this->getInput('InjuredPerson')),
            'Witnesses' => sanitize($this->getInput('Witnesses')),
            'RootCause' => sanitize($this->getInput('RootCause')),
            'CorrectiveActions' => sanitize($this->getInput('CorrectiveActions')),
            'Status' => sanitize($this->getInput('Status')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'accident', $id, 'Created accident report');
        setFlash('success', 'Accident report created successfully.');
        $this->redirect('safety/accidents');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('safety/accidents');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');
        $id = $this->getInput('AccidentID');

        $this->model->update($id, [
            'AccidentDate' => $this->getInput('AccidentDate'),
            'Location' => sanitize($this->getInput('Location')),
            'Description' => sanitize($this->getInput('Description')),
            'InjurySeverity' => sanitize($this->getInput('InjurySeverity')),
            'InjuredPerson' => sanitize($this->getInput('InjuredPerson')),
            'Witnesses' => sanitize($this->getInput('Witnesses')),
            'RootCause' => sanitize($this->getInput('RootCause')),
            'CorrectiveActions' => sanitize($this->getInput('CorrectiveActions')),
            'Status' => sanitize($this->getInput('Status')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'accident', $id, 'Updated accident report');
        setFlash('success', 'Accident report updated successfully.');
        $this->redirect('safety/accidents');
    }
}
