<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/SopModel.php';
require_once __DIR__ . '/../models/ProductionBatchModel.php';

class SopController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new SopModel();
        $this->viewPath = 'sops';
    }

    public function index(): void {
        $this->render('index', [
            'templates' => $this->model->getTemplates(),
            'checklists' => $this->model->getChecklists(),
        ]);
    }

    public function createTemplate(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->createTemplate([
                'SOP_ID' => $this->getInput('SOP_ID'), 'Title' => $this->getInput('title'),
                'Department' => $this->getInput('department'),
                'Version' => $this->getInput('version', '1.0'),
                'Content' => $this->getInput('content'),
                'EffectiveDate' => $this->getInput('effective_date'),
                'ReviewDate' => $this->getInput('review_date'),
                'Status' => $this->getInput('status', 'Active'),
                'CreatedBy' => $_SESSION['user_id'] ?? null,
            ]);
            setFlash('success', 'SOP template created.');
            $this->redirect('sops');
            return;
        }
        $this->render('template_form');
    }

    public function editTemplate(): void {
        $id = $this->getInput('id');
        $tpl = $this->model->findTemplate($id);
        if (!$tpl) { setFlash('error', 'Not found.'); $this->redirect('sops'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateTemplate($id, [
                'Title' => $this->getInput('title'), 'Department' => $this->getInput('department'),
                'Version' => $this->getInput('version'), 'Content' => $this->getInput('content'),
                'EffectiveDate' => $this->getInput('effective_date'),
                'ReviewDate' => $this->getInput('review_date'),
                'Status' => $this->getInput('status'),
            ]);
            setFlash('success', 'SOP updated.');
            $this->redirect('sops');
            return;
        }
        $this->render('template_form', ['tpl' => $tpl]);
    }

    public function deleteTemplate(): void {
        $this->model->deleteTemplate($this->getInput('id'));
        setFlash('success', 'SOP deleted.');
        $this->redirect('sops');
    }

    public function createChecklist(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->createChecklist([
                'ChecklistID' => $this->getInput('ChecklistID'),
                'SOP_ID' => $this->getInput('sop_id'),
                'BatchID' => $this->getInput('batch_id') ?: null,
                'Date' => $this->getInput('date'),
                'ChecklistItems' => $this->getInput('ChecklistItems'),
                'CompletedItems' => (int)$this->getInput('completed_items', '0'),
                'TotalItems' => (int)$this->getInput('total_items', '0'),
                'SupervisorID' => $this->getInput('supervisor_id') ?: ($_SESSION['user_id'] ?? null),
                'ApprovalStatus' => $this->getInput('approval_status', 'Pending'),
                'Notes' => $this->getInput('notes'),
            ]);
            setFlash('success', 'Checklist created.');
            $this->redirect('sops');
            return;
        }
        $this->render('checklist_form', [
            'templates' => $this->model->getTemplates(),
            'batches' => (new ProductionBatchModel())->all(),
        ]);
    }

    public function editChecklist(): void {
        $id = $this->getInput('id');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updateChecklist($id, [
                'CompletedItems' => (int)$this->getInput('completed_items', '0'),
                'ApprovalStatus' => $this->getInput('approval_status'),
                'Notes' => $this->getInput('notes'),
            ]);
            setFlash('success', 'Checklist updated.');
            $this->redirect('sops');
            return;
        }
        $this->render('checklist_form', ['templates' => $this->model->getTemplates(), 'batches' => (new ProductionBatchModel())->all()]);
    }
}
