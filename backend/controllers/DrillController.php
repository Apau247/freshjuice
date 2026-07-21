<?php
class DrillController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new DrillModel();
        $this->viewPath = 'safety';
    }

    public function index()
    {
        $data = [
            'drills' => $this->model->getAllDetailed(),
        ];
        $this->render('drill_index', $data);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('drill_form');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('DRL');
        $this->model->create([
            'DrillID' => $id,
            'DrillType' => sanitize($this->getInput('DrillType')),
            'DrillDate' => $this->getInput('DrillDate'),
            'Location' => sanitize($this->getInput('Location')),
            'ParticipantsCount' => (int) $this->getInput('ParticipantsCount'),
            'DurationMinutes' => (int) $this->getInput('DurationMinutes'),
            'Outcome' => sanitize($this->getInput('Outcome')),
            'IssuesFound' => sanitize($this->getInput('IssuesFound')),
            'CorrectiveAction' => sanitize($this->getInput('CorrectiveAction')),
            'ConductedBy' => sanitize($this->getInput('ConductedBy')),
            'Status' => sanitize($this->getInput('Status', 'Scheduled')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'drill', $id, 'Created drill record');
        setFlash('success', 'Drill record created successfully.');
        $this->redirect('safety/drills');
    }

    public function edit()
    {
        $id = $this->getInput('id');
        $drill = $this->model->find($id);

        if (!$drill) {
            setFlash('error', 'Drill record not found.');
            $this->redirect('safety/drills');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('drill_form', ['drill' => $drill]);
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $this->model->update($id, [
            'DrillType' => sanitize($this->getInput('DrillType')),
            'DrillDate' => $this->getInput('DrillDate'),
            'Location' => sanitize($this->getInput('Location')),
            'ParticipantsCount' => (int) $this->getInput('ParticipantsCount'),
            'DurationMinutes' => (int) $this->getInput('DurationMinutes'),
            'Outcome' => sanitize($this->getInput('Outcome')),
            'IssuesFound' => sanitize($this->getInput('IssuesFound')),
            'CorrectiveAction' => sanitize($this->getInput('CorrectiveAction')),
            'ConductedBy' => sanitize($this->getInput('ConductedBy')),
            'Status' => sanitize($this->getInput('Status')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'drill', $id, 'Updated drill record');
        setFlash('success', 'Drill record updated successfully.');
        $this->redirect('safety/drills');
    }

    public function delete()
    {
        $id = $this->getInput('id');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'drill', $id, 'Deleted drill record');
        setFlash('success', 'Drill record deleted successfully.');
        $this->redirect('safety/drills');
    }
}
