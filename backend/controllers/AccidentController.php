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
            $this->render('accident_form');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('ACC');
        $this->model->create([
            'AccidentID' => $id,
            'AccidentDate' => $this->getInput('IncidentDate'),
            'Location' => sanitize($this->getInput('Location')),
            'Description' => sanitize($this->getInput('Description')),
            'InjurySeverity' => sanitize($this->getInput('Injuries')),
            'InjuredPerson' => sanitize($this->getInput('InjuredPerson')),
            'Witnesses' => sanitize($this->getInput('Witnesses')),
            'RootCause' => sanitize($this->getInput('RootCause')),
            'CorrectiveActions' => sanitize($this->getInput('CorrectiveAction')),
            'Status' => sanitize($this->getInput('Status')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'accident', $id, 'Created accident report');
        setFlash('success', 'Accident report created successfully.');
        $this->redirect('safety/accidents');
    }

    public function edit()
    {
        $id = $this->getInput('id');
        $accident = $this->model->find($id);

        if (!$accident) {
            setFlash('error', 'Accident report not found.');
            $this->redirect('safety/accidents');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('accident_form', ['accident' => $accident]);
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $this->model->update($id, [
            'AccidentDate' => $this->getInput('IncidentDate'),
            'Location' => sanitize($this->getInput('Location')),
            'Description' => sanitize($this->getInput('Description')),
            'InjurySeverity' => sanitize($this->getInput('Injuries')),
            'InjuredPerson' => sanitize($this->getInput('InjuredPerson')),
            'Witnesses' => sanitize($this->getInput('Witnesses')),
            'RootCause' => sanitize($this->getInput('RootCause')),
            'CorrectiveActions' => sanitize($this->getInput('CorrectiveAction')),
            'Status' => sanitize($this->getInput('Status')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'accident', $id, 'Updated accident report');
        setFlash('success', 'Accident report updated successfully.');
        $this->redirect('safety/accidents');
    }
}
