<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class PayrollModel extends Model {
    protected string $table = 'payroll';
    protected string $primaryKey = 'PayrollID';

    public const MONTH_NAMES = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    /**
     * All payslips for a period. A payslip belongs to either a staff member
     * or a worker (laborer) — the LEFT JOINs + COALESCE handle both.
     */
    public function getForPeriod(int $month, int $year): array {
        return $this->query(
            "SELECT p.*,
                    COALESCE(s.FirstName, w.FirstName)  AS FirstName,
                    COALESCE(s.LastName,  w.LastName)   AS LastName,
                    COALESCE(s.Department, 'Workers')   AS Department,
                    COALESCE(s.Position,  w.Position)   AS Position,
                    p.StaffID  AS StaffCode,
                    p.WorkerID AS WorkerCode,
                    u.Name AS ProcessedByName
             FROM payroll p
             LEFT JOIN staff   s ON p.StaffID  = s.StaffID
             LEFT JOIN workers w ON p.WorkerID = w.WorkerID
             LEFT JOIN users u ON p.ProcessedBy = u.UserID
             WHERE p.PeriodMonth = ? AND p.PeriodYear = ?
             ORDER BY Department, FirstName",
            [$month, $year]
        );
    }

    /** One payslip with person context (edit screen). */
    public function findDetailed(string $id): ?array {
        return $this->queryOne(
            "SELECT p.*,
                    COALESCE(s.FirstName, w.FirstName) AS FirstName,
                    COALESCE(s.LastName,  w.LastName)  AS LastName,
                    COALESCE(s.Department, 'Workers')  AS Department,
                    COALESCE(s.Position,  w.Position)  AS Position
             FROM payroll p
             LEFT JOIN staff   s ON p.StaffID  = s.StaffID
             LEFT JOIN workers w ON p.WorkerID = w.WorkerID
             WHERE p.PayrollID = ?",
            [$id]
        );
    }

    /**
     * Generate Unpaid payslips for a period from each active staff member's
     * MonthlySalary AND each active worker's MonthlyPay. Existing slips for
     * the period are never duplicated.
     */
    public function generateForPeriod(int $month, int $year, ?string $userId): array {
        $people = [];

        foreach ($this->query(
            "SELECT StaffID AS Id, FirstName, LastName, MonthlySalary AS Pay
             FROM staff WHERE Status IN ('Active','On Leave')"
        ) as $s) {
            $people[] = ['id' => (string)$s['Id'], 'name' => trim($s['FirstName'] . ' ' . $s['LastName']),
                         'pay' => (float)$s['Pay'], 'type' => 'staff'];
        }
        foreach ($this->query(
            "SELECT WorkerID AS Id, FirstName, LastName, MonthlyPay AS Pay
             FROM workers WHERE Status IN ('Active','On Leave')"
        ) as $w) {
            $people[] = ['id' => (string)$w['Id'], 'name' => trim($w['FirstName'] . ' ' . $w['LastName']),
                         'pay' => (float)$w['Pay'], 'type' => 'worker'];
        }

        $existing = $this->query(
            "SELECT StaffID, WorkerID FROM payroll WHERE PeriodMonth = ? AND PeriodYear = ?",
            [$month, $year]
        );
        $have = [];
        foreach ($existing as $e) {
            if ($e['StaffID'])  $have['s:' . (string)$e['StaffID']]  = true;
            if ($e['WorkerID']) $have['w:' . (string)$e['WorkerID']] = true;
        }

        $result = ['created' => 0, 'skipped' => 0, 'no_salary' => []];
        foreach ($people as $p) {
            $key = ($p['type'] === 'staff' ? 's:' : 'w:') . $p['id'];
            if (isset($have[$key])) { $result['skipped']++; continue; }
            if ($p['pay'] <= 0) { $result['no_salary'][] = $p['name']; continue; }

            $this->create([
                'PayrollID'   => generateId('PAY'),
                'StaffID'     => $p['type'] === 'staff' ? $p['id'] : null,
                'WorkerID'    => $p['type'] === 'worker' ? $p['id'] : null,
                'PeriodMonth' => $month,
                'PeriodYear'  => $year,
                'BaseSalary'  => $p['pay'],
                'Allowances'  => 0,
                'Deductions'  => 0,
                'NetPay'      => round($p['pay'], 2),
                'Status'      => 'Unpaid',
                'ProcessedBy' => $userId,
            ]);
            $result['created']++;
        }
        return $result;
    }

    /** Mark one payslip as paid. */
    public function markPaid(string $id, string $method, string $date, ?string $userId, string $notes = ''): bool {
        return $this->update($id, [
            'Status'        => 'Paid',
            'PaymentDate'   => $date,
            'PaymentMethod' => $method,
            'Notes'         => $notes !== '' ? $notes : null,
            'ProcessedBy'   => $userId,
        ]);
    }

    /**
     * The login account to notify about a payment. Staff may have one;
     * workers never do (they are notified physically by the office).
     */
    public function getStaffUserId(string $payrollId): ?string {
        $r = $this->queryOne(
            "SELECT s.UserID FROM payroll p JOIN staff s ON p.StaffID = s.StaffID
             WHERE p.PayrollID = ? AND s.UserID IS NOT NULL",
            [$payrollId]
        );
        return $r ? (string)$r['UserID'] : null;
    }

    /** Revert a mistaken payment back to Unpaid. */
    public function markUnpaid(string $id, ?string $userId): bool {
        return $this->update($id, [
            'Status'        => 'Unpaid',
            'PaymentDate'   => null,
            'PaymentMethod' => null,
            'ProcessedBy'   => $userId,
        ]);
    }

    /** Paid vs unpaid totals + counts for a period (report header). */
    public function summary(int $month, int $year): array {
        $rows = $this->query(
            "SELECT Status,
                    COUNT(*)              AS cnt,
                    COALESCE(SUM(NetPay),0) AS total
             FROM payroll
             WHERE PeriodMonth = ? AND PeriodYear = ?
             GROUP BY Status",
            [$month, $year]
        );
        $summary = [
            'paid_cnt' => 0, 'paid_total' => 0.0,
            'unpaid_cnt' => 0, 'unpaid_total' => 0.0,
        ];
        foreach ($rows as $r) {
            if (($r['Status'] ?? '') === 'Paid') {
                $summary['paid_cnt'] = (int)$r['cnt'];
                $summary['paid_total'] = (float)$r['total'];
            } else {
                $summary['unpaid_cnt'] = (int)$r['cnt'];
                $summary['unpaid_total'] = (float)$r['total'];
            }
        }
        $summary['grand_total'] = $summary['paid_total'] + $summary['unpaid_total'];
        $summary['all_cnt'] = $summary['paid_cnt'] + $summary['unpaid_cnt'];
        return $summary;
    }

    /** Staff AND workers with their configured pay (settings + generate preview). */
    public function getStaffSalaries(): array {
        $out = [];
        foreach ($this->query(
            "SELECT s.StaffID AS Id, s.FirstName, s.LastName, s.Department, s.Position,
                    s.MonthlySalary AS Pay, s.Status, 'staff' AS PersonType
             FROM staff s
             WHERE s.Status IN ('Active','On Leave')"
        ) as $r) { $out[] = $r; }
        foreach ($this->query(
            "SELECT w.WorkerID AS Id, w.FirstName, w.LastName, 'Workers' AS Department, w.Position,
                    w.MonthlyPay AS Pay, w.Status, 'worker' AS PersonType
             FROM workers w
             WHERE w.Status IN ('Active','On Leave')"
        ) as $r) { $out[] = $r; }
        return $out;
    }

    /** Update a staff member's monthly salary. */
    public function setSalary(string $staffId, float $amount): bool {
        return $this->db->prepare("UPDATE staff SET MonthlySalary = ? WHERE StaffID = ?")
            ->execute([round($amount, 2), $staffId]);
    }

    /** Update a worker's monthly pay. */
    public function setWorkerPay(string $workerId, float $amount): bool {
        return $this->db->prepare("UPDATE workers SET MonthlyPay = ? WHERE WorkerID = ?")
            ->execute([round($amount, 2), $workerId]);
    }

    /** Which periods already have payroll runs? (period picker suggestions) */
    public function getAvailablePeriods(): array {
        return $this->query(
            "SELECT PeriodMonth, PeriodYear, COUNT(*) AS cnt
             FROM payroll
             GROUP BY PeriodYear, PeriodMonth
             ORDER BY PeriodYear DESC, PeriodMonth DESC"
        );
    }
}
