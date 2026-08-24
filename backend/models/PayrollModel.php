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

    /** All payslips for a period, joined with staff details. */
    public function getForPeriod(int $month, int $year): array {
        return $this->query(
            "SELECT p.*, s.FirstName, s.LastName, s.Department, s.Position,
                    s.StaffID AS StaffCode, u.Name AS ProcessedByName
             FROM payroll p
             JOIN staff s ON p.StaffID = s.StaffID
             LEFT JOIN users u ON p.ProcessedBy = u.UserID
             WHERE p.PeriodMonth = ? AND p.PeriodYear = ?
             ORDER BY s.Department, s.FirstName",
            [$month, $year]
        );
    }

    /** One payslip with staff context (edit screen). */
    public function findDetailed(string $id): ?array {
        return $this->queryOne(
            "SELECT p.*, s.FirstName, s.LastName, s.Department, s.Position
             FROM payroll p
             JOIN staff s ON p.StaffID = s.StaffID
             WHERE p.PayrollID = ?",
            [$id]
        );
    }

    /**
     * Generate Unpaid payslips for a period from each active staff member's
     * MonthlySalary. Existing slips for the period are never duplicated.
     * Returns ['created' => n, 'skipped' => n, 'no_salary' => names...].
     */
    public function generateForPeriod(int $month, int $year, ?string $userId): array {
        $staff = $this->query(
            "SELECT StaffID, FirstName, LastName, MonthlySalary
             FROM staff
             WHERE Status IN ('Active','On Leave')
             ORDER BY FirstName"
        );

        $existing = $this->query(
            "SELECT StaffID FROM payroll WHERE PeriodMonth = ? AND PeriodYear = ?",
            [$month, $year]
        );
        $have = [];
        foreach ($existing as $e) { $have[(string)$e['StaffID']] = true; }

        $result = ['created' => 0, 'skipped' => 0, 'no_salary' => []];
        foreach ($staff as $s) {
            if (isset($have[(string)$s['StaffID']])) { $result['skipped']++; continue; }
            $base = (float)($s['MonthlySalary'] ?? 0);
            if ($base <= 0) {
                $result['no_salary'][] = trim($s['FirstName'] . ' ' . $s['LastName']);
                continue;
            }
            $this->create([
                'PayrollID'   => generateId('PAY'),
                'StaffID'     => $s['StaffID'],
                'PeriodMonth' => $month,
                'PeriodYear'  => $year,
                'BaseSalary'  => $base,
                'Allowances'  => 0,
                'Deductions'  => 0,
                'NetPay'      => round($base, 2),
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

    /** Active/on-leave staff with their configured salaries (settings + generate preview). */
    public function getStaffSalaries(): array {
        return $this->query(
            "SELECT s.StaffID, s.FirstName, s.LastName, s.Department, s.Position,
                    s.MonthlySalary, s.Status
             FROM staff s
             WHERE s.Status IN ('Active','On Leave')
             ORDER BY s.Department, s.FirstName"
        );
    }

    public function setSalary(string $staffId, float $amount): bool {
        return $this->db->prepare("UPDATE staff SET MonthlySalary = ? WHERE StaffID = ?")
            ->execute([round($amount, 2), $staffId]);
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
