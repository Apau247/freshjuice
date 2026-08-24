<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ReportModel.php';

/**
 * Reports centre (SRS section 14): one hub page listing every report type,
 * one generic renderer with date filters + print + CSV export.
 */
class ReportController extends Controller {
    private ReportModel $reports;

    public function __construct() {
        parent::__construct();
        $this->model = new ReportModel();
        $this->reports = $this->model;
        $this->viewPath = 'reports';
    }

    public function index(): void {
        $this->guardReports();
        // Only the report cards this user's role AND department may open.
        $visible = $this->allowedTypes();
        $this->render('index', ['types' => $visible]);
    }

    public function show(): void {
        $this->guardReports();
        $type = $this->getInput('type');
        if (!isset(ReportModel::TYPES[$type]) || !isset($this->allowedTypes()[$type])) {
            setFlash('error', 'Unknown report or not available for your department.');
            $this->redirect('reports');
            return;
        }

        [$from, $to] = $this->range();
        $data = $this->reports->build($type, $from, $to);
        $data['type'] = $type;
        $data['from'] = $from;
        $data['to']   = $to;
        $data['pageTitle'] = $data['title'];
        $this->render('show', $data);
    }

    /**
     * Dedicated printable document: company letterhead + report name +
     * summary + results table only -- the app chrome is never printed.
     */
    public function printView(): void {
        $this->guardReports();
        $type = $this->getInput('type');
        if (!isset(ReportModel::TYPES[$type]) || !isset($this->allowedTypes()[$type])) {
            setFlash('error', 'Unknown report or not available for your department.');
            $this->redirect('reports');
            return;
        }
        [$from, $to] = $this->range();
        $data = $this->reports->build($type, $from, $to);
        $data['type'] = $type;
        $data['from'] = $from;
        $data['to']   = $to;
        logAudit($_SESSION['user_id'] ?? null, 'PRINT', 'Reports', $type, "Printed {$data['title']} ($from to $to)");
        $this->renderPrint('print', $data, $data['title']);
    }

    public function exportCsv(): void {
        if (!$this->reportsAllowed()) { $this->redirect('dashboard'); return; }
        $type = $this->getInput('type');
        if (!isset(ReportModel::TYPES[$type]) || !isset($this->allowedTypes()[$type])) {
            setFlash('error', 'Unknown report or not available for your department.');
            $this->redirect('reports');
            return;
        }
        [$from, $to] = $this->range();
        $data = $this->reports->build($type, $from, $to);

        if (!headers_sent()) {
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $type . '_report_' . $from . '_to_' . $to . '.csv"');
        }
        $out = fopen('php://output', 'w');
        fputcsv($out, [$data['title'], $data['period']]);
        fputcsv($out, []);
        foreach ($data['summary'] ?? [] as $label => $value) {
            fputcsv($out, [$label, $value]);
        }
        fputcsv($out, []);
        fputcsv($out, $data['headers'] ?? []);
        foreach ($data['rows'] ?? [] as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        logAudit($_SESSION['user_id'] ?? null, 'EXPORT', 'Reports', $type, "Exported {$data['title']} CSV");
        exit;
    }

    /** Reports centre is restricted to Administrator and Manager roles. */
    private function reportsAllowed(): bool {
        return hasRole(...ReportModel::REPORT_ROLES);
    }

    private function guardReports(): void {
        if (!$this->reportsAllowed()) {
            setFlash('error', 'Reports are restricted to administrators and managers.');
            $this->redirect('dashboard');
        }
    }

    /**
     * Partitioned report list for the signed-in manager: role gate, then
     * department partition, then the underlying module permission.
     */
    private function allowedTypes(): array {
        static $cached = null;
        if ($cached !== null) return $cached;
        $user = currentUser();
        $types = ReportModel::visibleTypes($user['role_id'] ?? '', $this->reports->departmentForUser($user['id'] ?? null));
        $cached = array_filter(
            $types,
            fn($t) => ReportModel::moduleFor($t) === null || can(ReportModel::moduleFor($t)),
            ARRAY_FILTER_USE_KEY
        );
        return $cached;
    }

    /** Validated date range (defaults to current month). */
    private function range(): array {
        $from = $this->getInput('from', date('Y-m-01'));
        $to   = $this->getInput('to', date('Y-m-d'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-01');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to = date('Y-m-d');
        if ($from > $to) [$from, $to] = [$to, $from];
        return [$from, $to];
    }
}
