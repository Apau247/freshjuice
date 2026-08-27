<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

/**
 * Workers are the factory laborers. They are NOT login users (USR-xxx)
 * and NOT office staff — they are managed by the Accountant via form or
 * Excel import, and they get paid through the payroll module.
 */
class WorkerModel extends Model
{
    protected string $table = 'workers';
    protected string $primaryKey = 'WorkerID';

    public function all(int $limit = 500): array {
        return $this->query(
            "SELECT * FROM workers
             ORDER BY Status ASC, LastName ASC, FirstName ASC
             LIMIT " . (int)$limit
        );
    }

    public function find(string $id): ?array {
        return $this->queryOne("SELECT * FROM workers WHERE WorkerID = ?", [$id]);
    }

    public function create(array $data): string {
        $id = generateId('WRK');
        $cols = implode(', ', array_keys(array_merge(['WorkerID' => $id], $data)));
        $phs  = implode(', ', array_fill(0, count($data) + 1, '?'));
        $stmt = $this->db->prepare("INSERT INTO workers ({$cols}) VALUES ({$phs})");
        $stmt->execute(array_values(array_merge(['WorkerID' => $id], $data)));
        return $id;
    }

    public function updateWorker(string $id, array $data): bool {
        return $this->update($id, $data);
    }

    public function deleteWorker(string $id): bool {
        return $this->delete($id);
    }

    /** Bulk-insert rows from the Excel import. Returns count inserted. */
    public function bulkCreate(array $rows): int {
        $db = getDb();
        $count = 0;
        $skipped = 0;
        $db->beginTransaction();
        try {
            $stmt = $db->prepare(
                "INSERT INTO workers (WorkerID, FirstName, LastName, Phone, Position, MonthlyPay, DateHired, Status)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            foreach ($rows as $row) {
                $row = array_change_key_case((array)$row, CASE_LOWER);
                // Broad column matching: try many common header variations
                $firstName = trim((string)(
                    $row['firstname'] ?? $row['first_name'] ?? $row['first'] ??
                    $row['fname'] ?? $row['given_name'] ?? $row['givenname'] ??
                    $row['worker_name'] ?? $row['workername'] ??
                    $row['name'] ?? $row['employee_name'] ?? $row['employeename'] ?? ''
                ));
                $lastName  = trim((string)(
                    $row['lastname'] ?? $row['last_name'] ?? $row['last'] ??
                    $row['lname'] ?? $row['family_name'] ?? $row['familyname'] ??
                    $row['surname'] ?? $row['second_name'] ?? $row['secondname'] ?? ''
                ));
                if ($firstName === '' && $lastName === '') {
                    $skipped++;
                    continue;
                }
                $status = ucfirst(strtolower(trim((string)($row['status'] ?? $row['work_status'] ?? 'Active'))));
                $dateHired = trim((string)(
                    $row['datehired'] ?? $row['date_hired'] ?? $row['hiredate'] ??
                    $row['hire_date'] ?? $row['start_date'] ?? $row['startdate'] ?? ''
                ));
                // Accept Excel serial dates and common formats
                if ($dateHired !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateHired)) {
                    $ts = strtotime($dateHired);
                    $dateHired = $ts ? date('Y-m-d', $ts) : null;
                }
                $stmt->execute([
                    generateId('WRK'),
                    $firstName,
                    $lastName,
                    trim((string)($row['phone'] ?? $row['phone_number'] ?? $row['telephone'] ?? $row['tel'] ?? $row['contact'] ?? '')) ?: null,
                    trim((string)($row['position'] ?? $row['job'] ?? $row['role'] ?? $row['job_title'] ?? $row['title'] ?? '')) ?: 'Laborer',
                    (float)($row['monthlypay'] ?? $row['monthly_pay'] ?? $row['pay'] ?? $row['salary'] ?? $row['monthly_salary'] ?? $row['wage'] ?? 0),
                    $dateHired ?: null,
                    in_array($status, ['Active', 'On Leave', 'Terminated'], true) ? $status : 'Active',
                ]);
                $count++;
            }
            $db->commit();
        } catch (\Exception $e) {
            $db->rollBack();
            throw $e;
        }
        if ($skipped > 0) {
            error_log("Worker import: skipped {$skipped} rows (empty first+last name)");
        }
        return $count;
    }

    public function countAll(): int {
        return (int)$this->queryScalar("SELECT COUNT(*) FROM workers");
    }

    public function countActive(): int {
        return (int)$this->queryScalar("SELECT COUNT(*) FROM workers WHERE Status = 'Active'");
    }

    /** Active/on-leave workers with their pay (payroll settings + generation). */
    public function getPayable(): array {
        return $this->query(
            "SELECT WorkerID, FirstName, LastName, Position, MonthlyPay, Status
             FROM workers
             WHERE Status IN ('Active','On Leave')
             ORDER BY FirstName"
        );
    }
}
