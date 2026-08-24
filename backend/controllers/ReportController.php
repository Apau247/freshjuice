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
        // Describe each report card with the modules the signed-in user can see.
        $types = ReportModel::TYPES;
        $visible = array_filter($types, fn($t) => self::moduleFor($t) === null || can(self::moduleFor($t)), ARRAY_FILTER_USE_KEY);
        $this->render('index', ['types' => $visible]);
    }

    public function show(): void {
        $type = $this->getInput('type');
        if (!isset(ReportModel::TYPES[$type])) {
            setFlash('error', 'Unknown report type.');
            $this->redirect('reports');
            return;
        }
        $module = ReportModel::moduleFor($type);
        if ($module && !can($module)) {
            setFlash('error', 'You do not have permission to view this report.');
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

    public function exportCsv(): void {
        $type = $this->getInput('type');
        if (!isset(ReportModel::TYPES[$type]) || !can('reports')) {
            $this->redirect('reports');
            return;
        }
        $module = ReportModel::moduleFor($type);
        if ($module && !can($module)) {
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
