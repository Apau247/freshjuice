<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/WorkerModel.php';

class WorkerController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = 'workers';
        $this->model    = new WorkerModel();
    }

    public function index(): void
    {
        $this->requireCan('workers');
        $this->render('index', [
            'workers'     => $this->model->all(),
            'total'       => $this->model->countAll(),
            'activeCount' => $this->model->countActive(),
        ]);
    }

    public function create(): void
    {
        $this->requireCanCreate('workers');
        $this->render('form', ['worker' => null, 'mode' => 'create']);
    }

    public function store(): void
    {
        $this->requireCanCreate('workers');
        $this->saveWorker(null);
        $this->redirect('workers');
    }

    public function edit(): void
    {
        $this->requireCanEdit('workers');
        $id = $this->getInput('id');
        $worker = $id ? $this->model->find($id) : null;
        if (!$worker) { setFlash('danger', 'Worker not found.'); $this->redirect('workers'); }
        $this->render('form', ['worker' => $worker, 'mode' => 'edit']);
    }

    public function update(): void
    {
        $this->requireCanEdit('workers');
        $this->saveWorker($this->getInput('id'));
        $this->redirect('workers');
    }

    public function delete(): void
    {
        $this->requireCanEdit('workers');
        $id = $this->getInput('id');
        if ($id && $this->model->deleteWorker($id)) {
            setFlash('success', 'Worker removed.');
        }
        $this->redirect('workers');
    }

    /** Show the Excel-import page. */
    public function import(): void
    {
        $this->requireCanCreate('workers');
        $this->render('import', []);
    }

    /** Download a template XLSX for workers import. */
    public function template(): void
    {
        $this->requireCanCreate('workers');
        $headers = ['FirstName', 'LastName', 'Phone', 'Position', 'MonthlyPay', 'DateHired', 'Status'];
        $sample  = ['Kofi', 'Asante', '0241234567', 'Juicer Operator', '1800', '2026-01-15', 'Active'];
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="workers_import_template.csv"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        $fp = fopen('php://output', 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel
        fputcsv($fp, $headers);
        fputcsv($fp, $sample);
        fclose($fp);
        exit;
    }

    /** Accept a JSON array of rows from the client-side Excel parser and bulk-insert. */
    public function importStore(): void
    {
        $this->requireCanCreate('workers');
        $rows = $this->getJsonInput();
        if (!is_array($rows) || empty($rows)) {
            jsonResponse(['ok' => false, 'error' => 'No data received.'], 400);
        }
        if (count($rows) > 500) {
            jsonResponse(['ok' => false, 'error' => 'Maximum 500 rows per import. Please split your file.'], 400);
        }
        try {
            $count = $this->model->bulkCreate($rows);
        } catch (\Exception $e) {
            error_log('Worker import failed: ' . $e->getMessage());
            jsonResponse(['ok' => false, 'error' => 'Import failed. Please check your file and try again.'], 500);
        }
        logAudit($_SESSION['user_id'] ?? null, 'IMPORT', 'Workers', null, "Imported {$count} workers via Excel");
        jsonResponse(['ok' => true, 'imported' => $count]);
    }

    private function saveWorker(?string $id): void
    {
        $firstName = trim($this->getInput('first_name'));
        $lastName  = trim($this->getInput('last_name'));
        if ($firstName === '' || $lastName === '') {
            setFlash('error', 'First name and last name are required.');
            $this->redirect($id ? 'workers/edit&id=' . urlencode($id) : 'workers/create');
            return;
        }
        if (mb_strlen($firstName) > 100 || mb_strlen($lastName) > 100) {
            setFlash('error', 'Name fields must be 100 characters or fewer.');
            $this->redirect($id ? 'workers/edit&id=' . urlencode($id) : 'workers/create');
            return;
        }
        $phone = trim($this->getInput('phone'));
        if ($phone !== '' && !preg_match('/^[\d\s\-+()]{7,30}$/', $phone)) {
            setFlash('error', 'Invalid phone number.');
            $this->redirect($id ? 'workers/edit&id=' . urlencode($id) : 'workers/create');
            return;
        }
        $pay = (float)$this->getInput('monthly_pay');
        if ($pay < 0 || $pay > 10000000) {
            setFlash('error', 'Monthly pay must be between 0 and 10,000,000.');
            $this->redirect($id ? 'workers/edit&id=' . urlencode($id) : 'workers/create');
            return;
        }
        $dateHired = $this->getInput('date_hired');
        if ($dateHired !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateHired)) {
            setFlash('error', 'Invalid date format.');
            $this->redirect($id ? 'workers/edit&id=' . urlencode($id) : 'workers/create');
            return;
        }

        $validStatus = ['Active','On Leave','Terminated'];
        $status = $this->getInput('status', 'Active');
        if (!in_array($status, $validStatus, true)) $status = 'Active';

        $data = [
            'FirstName'  => $firstName,
            'LastName'   => $lastName,
            'Phone'      => $phone ?: null,
            'Position'   => $this->getInput('position') ?: 'Laborer',
            'MonthlyPay' => round($pay, 2),
            'DateHired'  => $dateHired ?: null,
            'Status'     => $status,
        ];
        if ($id) {
            $this->model->updateWorker($id, $data);
            setFlash('success', 'Worker updated.');
        } else {
            $newId = $this->model->create($data);
            logAudit($_SESSION['user_id'] ?? null, 'CREATE', 'Workers', $newId, "Created worker {$firstName} {$lastName}");
            setFlash('success', 'Worker created.');
        }
    }
}
