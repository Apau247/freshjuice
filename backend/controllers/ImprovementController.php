<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ImprovementModel.php';

class ImprovementController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->model = new ImprovementModel();
        $this->viewPath = 'improvement';
    }

    public function index(): void
    {
        $data = [
            'initiatives' => $this->model->getAllDetailed(),
            'statusCounts' => $this->model->getStatusCounts(),
            'overdue' => $this->model->getOverdue(),
        ];
        $this->render('index', $data);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = generateId('IMP');
            $this->model->create([
                'InitiativeID' => $id,
                'Title' => $this->getInput('title'),
                'Category' => $this->getInput('category'),
                'Description' => $this->getInput('description'),
                'RootCauseAnalysis' => $this->getInput('root_cause'),
                'ActionPlan' => $this->getInput('action_plan'),
                'TargetDate' => $this->getInput('target_date'),
                'ResponsiblePerson' => $this->getInput('responsible_person'),
                'Status' => $this->getInput('status', 'Proposed'),
                'Effectiveness' => $this->getInput('effectiveness'),
                'CreatedBy' => $_SESSION['user_id'] ?? null,
            ]);
            logAudit($_SESSION['user_id'], 'CREATE', 'Improvement', $id, 'Created CAPA initiative');
            setFlash('success', 'Initiative created.');
            $this->redirect('improvement');
            return;
        }
        $this->render('form');
    }

    public function edit(): void
    {
        $id = $this->getInput('id');
        $item = $this->model->find($id);
        if (!$item) { setFlash('error', 'Not found.'); $this->redirect('improvement'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->update($id, [
                'Title' => $this->getInput('title'),
                'Category' => $this->getInput('category'),
                'Description' => $this->getInput('description'),
                'RootCauseAnalysis' => $this->getInput('root_cause'),
                'ActionPlan' => $this->getInput('action_plan'),
                'TargetDate' => $this->getInput('target_date'),
                'ResponsiblePerson' => $this->getInput('responsible_person'),
                'Status' => $this->getInput('status'),
                'Effectiveness' => $this->getInput('effectiveness'),
            ]);
            logAudit($_SESSION['user_id'], 'UPDATE', 'Improvement', $id, 'Updated CAPA initiative');
            setFlash('success', 'Initiative updated.');
            $this->redirect('improvement');
            return;
        }
        $this->render('form', ['initiative' => $item]);
    }

    public function delete(): void
    {
        $id = $this->getInput('id');
        $this->model->delete($id);
        logAudit($_SESSION['user_id'], 'DELETE', 'Improvement', $id, 'Deleted CAPA initiative');
        setFlash('success', 'Initiative deleted.');
        $this->redirect('improvement');
    }
}
