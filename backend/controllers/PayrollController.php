<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/PayrollModel.php';
require_once __DIR__ . '/../models/StaffModel.php';

/**
 * Employer payroll: the Administrator and Factory Manager set each staff
 * member's payment amount, generate monthly runs, and mark payslips as
 * paid/unpaid. Everyone else with payroll access sees the paid/not-paid
 * status read-only (index + report).
 */
class PayrollController extends Controller {
    private const PAYMENT_METHODS = ['Cash', 'Mobile Money', 'Bank Transfer', 'Cheque'];

    public function __construct() {
        parent::__construct();
        $this->model = new PayrollModel();
        $this->viewPath = 'payroll';
    }

    /** Resolve ?month/?year into a sane period, defaulting to the newest run. */
    private function resolvePeriod(): array {
        $month = (int)$this->getInput('month', (string)(int)date('n'));
        $year  = (int)$this->getInput('year', (string)(int)date('Y'));
        if ($month < 1 || $month > 12) $month = (int)date('n');
        if ($year < 2000 || $year > (int)date('Y') + 1) $year = (int)date('Y');
        return [$month, $year];
    }

    public static function monthName(int $m): string {
        return PayrollModel::MONTH_NAMES[$m] ?? (string)$m;
    }

    /* ── Payslip list for a period ─────────────────────────────── */

    public function index(): void {
        [$month, $year] = $this->resolvePeriod();

        // No query string at all -> land on the most recent run that exists.
        if (!isset($_GET['month'], $_GET['year'])) {
            $periods = $this->model->getAvailablePeriods();
            if ($periods && !empty($periods[0])) {
                $month = (int)$periods[0]['PeriodMonth'];
                $year  = (int)$periods[0]['PeriodYear'];
            }
        }

        $this->render('index', [
            'records' => $this->model->getForPeriod($month, $year),
            'summary' => $this->model->summary($month, $year),
            'periods' => $this->model->getAvailablePeriods(),
            'month'   => $month,
            'year'    => $year,
            'methods' => self::PAYMENT_METHODS,
            'today'   => date('Y-m-d'),
        ]);
    }

    /* ── Generate a monthly run from staff salaries ─────────────── */

    public function generate(): void {
        $this->requireCanEdit('payroll');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            [$month, $year] = $this->resolvePeriod();

            if ($year > (int)date('Y') || ($year === (int)date('Y') && $month > (int)date('n'))) {
                setFlash('error', 'Cannot run payroll for a future period.');
                $this->redirect('payroll/generate');
                return;
            }

            $result = $this->model->generateForPeriod($month, $year, $_SESSION['user_id'] ?? null);
            logAudit($_SESSION['user_id'] ?? null, 'CREATE', 'Payroll',
                "P-{$year}-" . str_pad((string)$month, 2, '0', STR_PAD_LEFT),
                "Generated payroll run: {$result['created']} created, {$result['skipped']} existing");

            $msg = "Payroll run complete — {$result['created']} payslip(s) created";
            if ($result['skipped'] > 0) $msg .= ", {$result['skipped']} already existed";
            if ($result['no_salary']) {
                $msg .= '. Skipped (no salary set): ' . implode(', ', array_slice($result['no_salary'], 0, 5));
                if (count($result['no_salary']) > 5) $msg .= ' +' . (count($result['no_salary']) - 5) . ' more';
            }
            setFlash($result['created'] > 0 ? 'success' : 'info', $msg);
            $this->redirect("payroll&month={$month}&year={$year}");
            return;
        }

        [$month, $year] = $this->resolvePeriod();
        $salaries = $this->model->getStaffSalaries();
        $this->render('generate', [
            'staff'    => $salaries,
            'existing' => array_column(
                $this->model->query("SELECT StaffID FROM payroll WHERE PeriodMonth=? AND PeriodYear=?", [$month, $year]),
                'StaffID'
            ),
            'estimated' => array_sum(array_map(fn ($s) => (float)($s['MonthlySalary'] ?? 0), $salaries)),
            'month'    => $month,
            'year'     => $year,
        ]);
    }

    /* ── Mark one payslip as paid ───────────────────────────────── */

    public function pay(): void {
        $this->requireCanEdit('payroll');
        $id = $this->getInput('id');
        $slip = $this->model->find($id);
        if (!$slip) { setFlash('error', 'Payroll record not found.'); $this->redirect('payroll'); return; }
        if (($slip['Status'] ?? '') === 'Paid') {
            setFlash('error', 'This payslip is already marked as paid.');
            $this->redirect("payroll&month={$slip['PeriodMonth']}&year={$slip['PeriodYear']}");
            return;
        }

        $method = $this->getInput('payment_method', 'Cash');
        if (!in_array($method, self::PAYMENT_METHODS, true)) $method = 'Cash';
        $date = $this->getInput('payment_date') ?: date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $date > date('Y-m-d')) $date = date('Y-m-d');

        $this->model->markPaid($id, $method, $date, $_SESSION['user_id'] ?? null, $this->getInput('notes'));
        logAudit($_SESSION['user_id'] ?? null, 'PAYMENT', 'Payroll', $id,
            "Marked payroll {$id} as PAID via {$method} on {$date}");
        setFlash('success', 'Payment recorded — payslip marked as PAID.');
        $this->redirect("payroll&month={$slip['PeriodMonth']}&year={$slip['PeriodYear']}");
    }

    /* ── Revert a mistaken payment ──────────────────────────────── */

    public function revert(): void {
        $this->requireCanEdit('payroll'); // Administrator + Factory Manager only
        $id = $this->getInput('id');
        $slip = $this->model->find($id);
        if (!$slip) { setFlash('error', 'Payroll record not found.'); $this->redirect('payroll'); return; }

        $this->model->markUnpaid($id, $_SESSION['user_id'] ?? null);
        logAudit($_SESSION['user_id'] ?? null, 'UPDATE', 'Payroll', $id, "Reverted payroll {$id} to UNPAID");
        setFlash('success', 'Payslip reverted to UNPAID.');
        $this->redirect("payroll&month={$slip['PeriodMonth']}&year={$slip['PeriodYear']}");
    }

    /* ── Adjust one payslip (bonus/deduction/pro-rating) ────────── */

    public function edit(): void {
        $this->requireCanEdit('payroll');
        $id = $this->getInput('id');
        $slip = $this->model->findDetailed($id);
        if (!$slip) { setFlash('error', 'Payroll record not found.'); $this->redirect('payroll'); return; }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (($slip['Status'] ?? '') === 'Paid') {
                setFlash('error', 'Paid payslips are locked. Revert the payment before editing.');
                $this->redirect("payroll/edit&id=" . urlencode($id));
                return;
            }
            $base = (float)$this->getInput('base_salary', '0');
            $allow = (float)$this->getInput('allowances', '0');
            $deduct = (float)$this->getInput('deductions', '0');

            $err = $this->checkNumber('Base salary', $base, 0, 1000000)
                ?? $this->checkNumber('Allowances', $allow, 0, 1000000)
                ?? $this->checkNumber('Deductions', $deduct, 0, 1000000);
            if ($err) {
                setFlash('error', $err);
                $this->redirect("payroll/edit&id=" . urlencode($id));
                return;
            }

            $this->model->update($id, [
                'BaseSalary' => round($base, 2),
                'Allowances' => round($allow, 2),
                'Deductions' => round($deduct, 2),
                'NetPay'     => round(max($base + $allow - $deduct, 0), 2),
                'Notes'      => mb_substr($this->getInput('notes'), 0, 500),
                'ProcessedBy' => $_SESSION['user_id'] ?? null,
            ]);
            logAudit($_SESSION['user_id'] ?? null, 'UPDATE', 'Payroll', $id, "Adjusted payslip {$id}");
            setFlash('success', 'Payslip updated.');
            $this->redirect("payroll&month={$slip['PeriodMonth']}&year={$slip['PeriodYear']}");
            return;
        }

        $this->render('form', ['slip' => $slip]);
    }

    public function delete(): void {
        $this->requireCanEdit('payroll');
        $id = $this->getInput('id');
        $slip = $this->model->find($id);
        if (!$slip) { setFlash('error', 'Payroll record not found.'); $this->redirect('payroll'); return; }
        if (($slip['Status'] ?? '') === 'Paid') {
            setFlash('error', 'Paid payslips cannot be deleted. Revert the payment first.');
            $this->redirect("payroll&month={$slip['PeriodMonth']}&year={$slip['PeriodYear']}");
            return;
        }
        $this->model->delete($id);
        logAudit($_SESSION['user_id'] ?? null, 'DELETE', 'Payroll', $id, "Deleted unpaid payslip {$id}");
        setFlash('success', 'Payslip deleted.');
        $this->redirect("payroll&month={$slip['PeriodMonth']}&year={$slip['PeriodYear']}");
    }

    /* ── Salary settings (admin sets each staff payment amount) ─── */

    public function settings(): void {
        $this->requireCanEdit('payroll'); // Administrator + Factory Manager only

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $amounts = json_decode($this->getInput('salaries', ''), true);
            if (!is_array($amounts)) $amounts = [];

            $updated = 0; $errors = [];
            foreach ($amounts as $staffId => $amt) {
                $amount = (float)$amt;
                if ($amount < 0 || !is_finite($amount) || $amount > 10000000) {
                    $errors[] = $staffId;
                    continue;
                }
                $this->model->setSalary((string)$staffId, $amount);
                $updated++;
            }
            logAudit($_SESSION['user_id'] ?? null, 'UPDATE', 'Payroll', null, "Updated salary settings for {$updated} staff");
            if ($errors) {
                setFlash('error', "Saved {$updated} salaries; " . count($errors) . ' value(s) were invalid and skipped.');
            } else {
                setFlash('success', "Salary settings saved for {$updated} staff member(s).");
            }
            $this->redirect('payroll/settings');
            return;
        }

        $this->render('settings', [
            'staff' => $this->model->getStaffSalaries(),
        ]);
    }

    /* ── Paid vs Not-Paid report ────────────────────────────────── */

    public function report(): void {
        [$month, $year] = $this->resolvePeriod();
        $records = $this->model->getForPeriod($month, $year);

        $paid = []; $unpaid = [];
        foreach ($records as $r) {
            if (($r['Status'] ?? '') === 'Paid') $paid[] = $r; else $unpaid[] = $r;
        }

        $this->render('report', [
            'paid'    => $paid,
            'unpaid'  => $unpaid,
            'summary' => $this->model->summary($month, $year),
            'month'   => $month,
            'year'    => $year,
        ]);
    }

    /** Printable version of the paid/unpaid report (bare letterhead layout). */
    public function printReport(): void {
        [$month, $year] = $this->resolvePeriod();
        $records = $this->model->getForPeriod($month, $year);

        $paid = []; $unpaid = [];
        foreach ($records as $r) {
            if (($r['Status'] ?? '') === 'Paid') $paid[] = $r; else $unpaid[] = $r;
        }

        logAudit($_SESSION['user_id'] ?? null, 'PRINT', 'Payroll',
            "P-{$year}-" . str_pad((string)$month, 2, '0', STR_PAD_LEFT),
            'Printed payroll payment report');
        $this->renderPrint('report_print', [
            'paid'    => $paid,
            'unpaid'  => $unpaid,
            'summary' => $this->model->summary($month, $year),
            'month'   => $month,
            'year'    => $year,
        ], 'Payroll Payment Report — ' . self::monthName($month) . " {$year}");
    }
}
