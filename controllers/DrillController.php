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
            $this->redirect('safety/drills');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');

        $id = generateId('DRL');
        $this->model->create([
            'DrillID' => $id,
            'DrillType' => sanitize($this->getInput('DrillType')),
            'DrillDate' => $this->getInput('DrillDate'),
            'Location' => sanitize($this->getInput('Location')),
            'Participants' => (int) $this->getInput('ParticipantsCount'),
            'Duration' => $this->getInput('DurationMinutes'),
            'Outcome' => sanitize($this->getInput('Outcome')),
            'IssuesFound' => sanitize($this->getInput('IssuesFound')),
            'CorrectiveActions' => sanitize($this->getInput('CorrectiveAction')),
            'ConductedBy' => sanitize($this->getInput('ConductedBy')),
        ]);

        logAudit($_SESSION['user_id'], 'create', 'drill', $id, 'Created drill record');
        setFlash('success', 'Drill record created successfully.');
        $this->redirect('safety/drills');
    }

    public function edit()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('safety/drills');
            return;
        }

        $this->requireRole('admin', 'manager', 'supervisor');
        $id = $this->getInput('DrillID');

        $this->model->update($id, [
            'DrillType' => sanitize($this->getInput('DrillType')),
            'DrillDate' => $this->getInput('DrillDate'),
            'Location' => sanitize($this->getInput('Location')),
            'Participants' => (int) $this->getInput('ParticipantsCount'),
            'Duration' => $this->getInput('DurationMinutes'),
            'Outcome' => sanitize($this->getInput('Outcome')),
            'IssuesFound' => sanitize($this->getInput('IssuesFound')),
            'CorrectiveActions' => sanitize($this->getInput('CorrectiveAction')),
            'ConductedBy' => sanitize($this->getInput('ConductedBy')),
        ]);

        logAudit($_SESSION['user_id'], 'update', 'drill', $id, 'Updated drill record');
        setFlash('success', 'Drill record updated successfully.');
        $this->redirect('safety/drills');
    }

    public function delete()
    {
        $id = $this->getInput('DrillID');
        $this->model->delete($id);

        logAudit($_SESSION['user_id'], 'delete', 'drill', $id, 'Deleted drill record');
        setFlash('success', 'Drill record deleted successfully.');
        $this->redirect('safety/drills');
    }
}
