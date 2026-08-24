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
            $id = generateId('SOP');
            $this->model->createTemplate([
                'SOP_ID' => $id, 'Title' => $this->getInput('title'),
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
        $this->render('template_form', ['template' => $tpl]);
    }

    public function deleteTemplate(): void {
        $this->model->deleteTemplate($this->getInput('id'));
        setFlash('success', 'SOP deleted.');
        $this->redirect('sops');
    }

    public function createChecklist(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $totalItems = (int)$this->getInput('total_items', '0');
            $completedItems = (int)$this->getInput('completed_items', '0');
            $err = $this->checkNumber('Total items', $totalItems, 0)
                ?? $this->checkNumber('Completed items', $completedItems, 0, $totalItems);
            if ($err) {
                setFlash('error', $err);
                $this->redirect('sops/checklist/form');
                return;
            }
            $id = generateId('CHK');
            $this->model->createChecklist([
                'ChecklistID' => $id,
                'SOP_ID' => $this->getInput('sop_id'),
                'BatchID' => $this->getInput('batch_id') ?: null,
                'Date' => $this->getInput('date'),
                // ChecklistItems is a MySQL JSON column: an empty string fails the
                // JSON validity check and aborts the INSERT with a PDOException.
                // Store a valid JSON array (or NULL when nothing was submitted).
                'ChecklistItems' => $this->checklistItemsJson(),
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
            'users' => (new UserModel())->all(),
        ]);
    }

    public function editChecklist(): void {
        $id = $this->getInput('id');
        $checklist = $this->model->findChecklist($id);

        if (!$checklist) {
            setFlash('error', 'Checklist not found.');
            $this->redirect('sops');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $totalItems = max(0, (int)($checklist['TotalItems'] ?? 0));
            $err = $this->checkNumber('Completed items', (int)$this->getInput('completed_items', '0'), 0, $totalItems);
            if ($err) {
                setFlash('error', $err);
                $this->redirect('sops/checklist/edit&id=' . urlencode($id));
                return;
            }
            $this->model->updateChecklist($id, [
                'CompletedItems' => (int)$this->getInput('completed_items', '0'),
                'ApprovalStatus' => $this->getInput('approval_status'),
                'Notes' => $this->getInput('notes'),
            ]);
            setFlash('success', 'Checklist updated.');
            $this->redirect('sops');
            return;
        }
        $this->render('checklist_form', [
            'checklist' => $checklist,
            'templates' => $this->model->getTemplates(),
            'batches' => (new ProductionBatchModel())->all(),
            'users' => (new UserModel())->all(),
        ]);
    }

    public function deleteChecklist(): void {
        $this->model->deleteChecklist($this->getInput('id'));
        setFlash('success', 'Checklist deleted.');
        $this->redirect('sops');
    }

    /**
     * Normalise the submitted checklist payload into valid JSON for the
     * sop_checklists.ChecklistItems JSON column. Accepts either an array of
     * item strings (e.g. items[] checkboxes) or a JSON/text blob; anything
     * empty becomes NULL so MySQL's JSON validation never rejects the row.
     */
    private function checklistItemsJson(): ?string {
        $raw = $_POST['ChecklistItems'] ?? $_POST['checklist_items'] ?? null;

        if (is_array($raw)) {
            $items = array_values(array_filter(array_map(
                fn($v) => trim((string)$v), $raw
            ), fn($v) => $v !== ''));
        } else {
            $text = trim((string)$raw);
            if ($text === '') {
                return null;
            }
            $decoded = json_decode($text, true);
            $items = is_array($decoded) ? $decoded : array_values(array_filter(
                array_map('trim', preg_split('/\r\n|\r|\n/', $text) ?: []),
                fn($v) => $v !== ''
            ));
        }

        return empty($items) ? null : json_encode($items, JSON_UNESCAPED_UNICODE);
    }
}
