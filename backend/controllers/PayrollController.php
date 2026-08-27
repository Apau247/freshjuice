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

    /** Notify the staff member that their salary has been paid (if they have a user account). */
    private function notifyPayment(string $payrollId, float $amount, string $monthName, string $year, string $method): void {
        $targetUserId = $this->model->getStaffUserId($payrollId);
        if ($targetUserId) {
            sendNotification($targetUserId, 'Salary Paid — ' . $monthName . " {$year}",
                'Your salary of ' . money($amount) . ' for ' . $monthName . " {$year} has been paid via {$method}."
                . ' Please collect your payslip from the payroll office.');
        }
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
        $existingRows = $this->model->query(
            "SELECT StaffID, WorkerID FROM payroll WHERE PeriodMonth=? AND PeriodYear=?",
            [$month, $year]
        );
        $existing = [];
        foreach ($existingRows as $e) {
            if ($e['StaffID'])  $existing[] = 's:' . $e['StaffID'];
            if ($e['WorkerID']) $existing[] = 'w:' . $e['WorkerID'];
        }
        $this->render('generate', [
            'staff'    => $salaries,
            'existing' => $existing,
            'estimated' => array_sum(array_map(fn ($s) => (float)($s['Pay'] ?? 0), $salaries)),
            'month'    => $month,
            'year'     => $year,
        ]);
    }

    /* ── Mark one payslip as paid ───────────────────────────────── */

    public function pay(): void {
        $this->requireCanEdit('payroll');
        $id = $this->getInput('id');
        if (!$id) { setFlash('error', 'Invalid request.'); $this->redirect('payroll'); return; }

        $db = getDb();
        $db->beginTransaction();
        try {
            $slip = $this->model->lock($id);
            if (!$slip) {
                $db->rollBack();
                setFlash('error', 'Payroll record not found.');
                $this->redirect('payroll');
                return;
            }
            if (($slip['Status'] ?? '') === 'Paid') {
                $db->rollBack();
                setFlash('error', 'This payslip is already marked as paid.');
                $this->redirect("payroll&month={$slip['PeriodMonth']}&year={$slip['PeriodYear']}");
                return;
            }

            $method = $this->getInput('payment_method', 'Cash');
            if (!in_array($method, self::PAYMENT_METHODS, true)) $method = 'Cash';
            $date = $this->getInput('payment_date') ?: date('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $date > date('Y-m-d')) $date = date('Y-m-d');

            $this->model->markPaid($id, $method, $date, $_SESSION['user_id'] ?? null, $this->getInput('notes'));
            $this->notifyPayment($id, (float)$slip['NetPay'], self::monthName((int)$slip['PeriodMonth']), (string)$slip['PeriodYear'], $method);
            logAudit($_SESSION['user_id'] ?? null, 'PAYMENT', 'Payroll', $id,
                "Marked payroll {$id} as PAID via {$method} on {$date}");

            $db->commit();
            setFlash('success', 'Payment recorded — payslip marked as PAID.');
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('Payroll pay failed: ' . $e->getMessage());
            setFlash('error', 'Payment failed. Please try again.');
        }
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

    /* ── Quick pay from Salary Settings: set the amount and pay in one go ── */

    public function payStaff(): void {
        $this->requireCanEdit('payroll');
        // Quick-pay targets either a staff member or a worker (laborer).
        $personType = $this->getInput('person_type', 'staff') === 'worker' ? 'worker' : 'staff';
        $personId   = $this->getInput('person_id', $this->getInput('staff_id'));

        if ($personType === 'worker') {
            $person = $this->model->queryOne(
                "SELECT WorkerID AS Id, FirstName, LastName, MonthlyPay AS Rate FROM workers WHERE WorkerID = ?",
                [$personId]
            );
        } else {
            $person = $this->model->queryOne(
                "SELECT StaffID AS Id, FirstName, LastName, MonthlySalary AS Rate FROM staff WHERE StaffID = ?",
                [$personId]
            );
        }
        if (!$person) { setFlash('error', 'Person not found.'); $this->redirect('payroll/settings'); return; }
        $personId = (string)$person['Id'];
        $fullName = trim($person['FirstName'] . ' ' . $person['LastName']);

        [$month, $year] = $this->resolvePeriod();
        if ($year > (int)date('Y') || ($year === (int)date('Y') && $month > (int)date('n'))) {
            setFlash('error', 'Cannot pay for a future period.');
            $this->redirect('payroll/settings');
            return;
        }

        $salaryRaw = trim((string)($_POST['salary'] ?? ''));
        $amountRaw = trim((string)($_POST['amount'] ?? $_GET['amount'] ?? ''));

        $newSalary = null;
        if ($salaryRaw !== '') {
            $candidate = (float)$salaryRaw;
            $err = $this->checkNumber('Monthly salary', $candidate, 0, 10000000);
            if ($err) { setFlash('error', $err . ' Nothing was saved.'); $this->redirect('payroll/settings'); return; }
            $newSalary = round($candidate, 2);
        }
        $salary = $newSalary ?? round((float)$person['Rate'], 2);

        if ($amountRaw === '') {
            setFlash('error', 'Enter the amount to pay. Nothing was saved.');
            $this->redirect('payroll/settings');
            return;
        }
        $payAmount = (float)$amountRaw;
        $err = $this->checkNumber('Amount to pay', $payAmount, 0.01, 10000000);
        if ($err) { setFlash('error', $err . ' Nothing was saved.'); $this->redirect('payroll/settings'); return; }
        $payAmount = round($payAmount, 2);

        // The payroll column this person lives in.
        $idCol    = $personType === 'worker' ? 'WorkerID' : 'StaffID';
        $oldRate  = round((float)$person['Rate'], 2);

        $db = getDb();
        $db->beginTransaction();
        try {
            if ($slipPaidCheck = $this->model->queryOne(
                "SELECT PayrollID, NetPay FROM payroll WHERE {$idCol} = ? AND PeriodMonth = ? AND PeriodYear = ? AND Status = 'Paid' FOR UPDATE",
                [$personId, $month, $year]
            )) {
                $db->rollBack();
                setFlash('info', "{$fullName} is already PAID for "
                    . self::monthName($month) . " {$year} (" . money($slipPaidCheck['NetPay']) . ").");
                $this->redirect('payroll/settings');
                return;
            }

            if ($newSalary !== null && $newSalary !== $oldRate) {
                if ($personType === 'worker') { $this->model->setWorkerPay($personId, $newSalary); }
                else { $this->model->setSalary($personId, $newSalary); }
                logAudit($_SESSION['user_id'] ?? null, 'UPDATE', 'Payroll', null,
                    "Set monthly pay of {$fullName} ({$personId}) to {$newSalary}");
            }

            $slip = $this->model->queryOne(
                "SELECT * FROM payroll WHERE {$idCol} = ? AND PeriodMonth = ? AND PeriodYear = ? FOR UPDATE",
                [$personId, $month, $year]
            );
            if ($slip) {
                $id = $slip['PayrollID'];
                $allowances = (float)$slip['Allowances'];
                $deductions = (float)$slip['Deductions'];
                $currentNet = round(max($salary + $allowances - $deductions, 0), 2);
                $diff = round($payAmount - $currentNet, 2);
                if ($diff >= 0) { $allowances = round($allowances + $diff, 2); }
                else { $deductions = round($deductions + abs($diff), 2); }
                $this->model->update($id, [
                    'BaseSalary' => $salary,
                    'Allowances' => $allowances,
                    'Deductions' => $deductions,
                    'NetPay'     => $payAmount,
                ]);
            } else {
                $id = generateId('PAY');
                $delta = round($payAmount - $salary, 2);
                $this->model->create([
                    'PayrollID'   => $id,
                    'StaffID'     => $personType === 'staff' ? $personId : null,
                    'WorkerID'    => $personType === 'worker' ? $personId : null,
                    'PeriodMonth' => $month,
                    'PeriodYear'  => $year,
                    'BaseSalary'  => $salary,
                    'Allowances'  => $delta > 0 ? $delta : 0,
                    'Deductions'  => $delta < 0 ? abs($delta) : 0,
                    'NetPay'      => $payAmount,
                    'Status'      => 'Unpaid',
                    'ProcessedBy' => $_SESSION['user_id'] ?? null,
                ]);
            }

            $method = $this->getInput('payment_method', 'Cash');
            if (!in_array($method, ['Cash', 'Mobile Money', 'Bank Transfer', 'Cheque'], true)) $method = 'Cash';
            $date = $this->getInput('payment_date') ?: date('Y-m-d');
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $date > date('Y-m-d')) $date = date('Y-m-d');

            $this->model->markPaid($id, $method, $date, $_SESSION['user_id'] ?? null, $this->getInput('notes'));
            $this->notifyPayment($id, $payAmount, self::monthName($month), (string)$year, $method);

            $db->commit();

            $detail = $payAmount === $salary
                ? money($payAmount)
                : money($payAmount) . ' (pay rate ' . money($salary) . ')';
            logAudit($_SESSION['user_id'] ?? null, 'PAYMENT', 'Payroll', $id,
                "Quick-paid {$fullName} for " . self::monthName($month)
                . " {$year}: {$detail} via {$method} on {$date}");
            setFlash('success', "{$fullName} PAID " . money($payAmount)
                . ' for ' . self::monthName($month) . " {$year} via {$method}."
                . ($newSalary !== null && $newSalary !== $oldRate
                    ? ' Pay rate updated to ' . money($newSalary) . '.' : ''));
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('Payroll payStaff failed: ' . $e->getMessage());
            setFlash('error', 'Payment failed. Please try again.');
        }
        $this->redirect('payroll/settings');
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
            $amounts = $this->getJsonInput('salaries');
            if ($amounts === null) $amounts = [];

            $updated = 0; $errors = [];
            foreach ($amounts as $personKey => $amt) {
                $amount = (float)$amt;
                // Keys are prefixed "s:STF-xxx" (staff) or "w:WRK-xxx" (worker).
                $type = str_starts_with((string)$personKey, 'w:') ? 'worker' : 'staff';
                $id   = preg_replace('/^[sw]:/', '', (string)$personKey);
                if ($amount < 0 || !is_finite($amount) || $amount > 10000000) {
                    $errors[] = $id;
                    continue;
                }
                if ($type === 'worker') {
                    $this->model->setWorkerPay($id, $amount);
                } else {
                    $this->model->setSalary($id, $amount);
                }
                $updated++;
            }
            logAudit($_SESSION['user_id'] ?? null, 'UPDATE', 'Payroll', null, "Updated pay settings for {$updated} people");
            if ($errors) {
                setFlash('error', "Saved {$updated} pay rate(s); " . count($errors) . ' value(s) were invalid and skipped.');
            } else {
                setFlash('success', "Pay settings saved for {$updated} staff/worker(s).");
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
