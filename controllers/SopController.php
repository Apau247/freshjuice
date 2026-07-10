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
                'SOP_ID' => $this->getInput('SOP_ID'), 'Title' => $this->getInput('Title'),
                'Department' => $this->getInput('Department'),
                'Version' => $this->getInput('Version', '1.0'),
                'Content' => $this->getInput('Content'),
                'EffectiveDate' => $this->getInput('EffectiveDate'),
                'ReviewDate' => $this->getInput('ReviewDate'),
                'Status' => $this->getInput('Status', 'Active'),
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
                'Title' => $this->getInput('Title'), 'Department' => $this->getInput('Department'),
                'Version' => $this->getInput('Version'), 'Content' => $this->getInput('Content'),
                'EffectiveDate' => $this->getInput('EffectiveDate'),
                'ReviewDate' => $this->getInput('ReviewDate'),
                'Status' => $this->getInput('Status'),
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
                'SOP_ID' => $this->getInput('SOP_ID'),
                'BatchID' => $this->getInput('BatchID') ?: null,
                'Date' => $this->getInput('Date'),
                'ChecklistItems' => $this->getInput('ChecklistItems'),
                'CompletedItems' => (int)$this->getInput('CompletedItems', '0'),
                'TotalItems' => (int)$this->getInput('TotalItems', '0'),
                'SupervisorID' => $_SESSION['user_id'] ?? null,
                'ApprovalStatus' => $this->getInput('ApprovalStatus', 'Pending'),
                'Notes' => $this->getInput('Notes'),
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
                'CompletedItems' => (int)$this->getInput('CompletedItems', '0'),
                'ApprovalStatus' => $this->getInput('ApprovalStatus'),
                'Notes' => $this->getInput('Notes'),
            ]);
            setFlash('success', 'Checklist updated.');
            $this->redirect('sops');
            return;
        }
        $this->render('checklist_form', ['templates' => $this->model->getTemplates(), 'batches' => (new ProductionBatchModel())->all()]);
    }
}
