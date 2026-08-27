<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class WorkerShiftModel extends Model
{
    protected string $table = 'worker_shift_assignments';
    protected string $primaryKey = 'AssignmentID';

    public function all(int $limit = 500): array {
        return $this->query(
            "SELECT a.*, w.FirstName, w.LastName, w.Position, s.ShiftName, s.StartTime, s.EndTime
             FROM worker_shift_assignments a
             JOIN workers w ON a.WorkerID = w.WorkerID
             JOIN shifts s ON a.ShiftID = s.ShiftID
             ORDER BY a.ShiftDate DESC, s.StartTime ASC, w.LastName ASC
             LIMIT " . (int)$limit
        );
    }

    public function find(string $id): ?array {
        return $this->queryOne(
            "SELECT a.*, w.FirstName, w.LastName, s.ShiftName, s.StartTime, s.EndTime
             FROM worker_shift_assignments a
             JOIN workers w ON a.WorkerID = w.WorkerID
             JOIN shifts s ON a.ShiftID = s.ShiftID
             WHERE a.AssignmentID = ?",
            [$id]
        );
    }

    public function getByDate(string $date): array {
        return $this->query(
            "SELECT a.*, w.FirstName, w.LastName, w.Position, s.ShiftName, s.StartTime, s.EndTime
             FROM worker_shift_assignments a
             JOIN workers w ON a.WorkerID = w.WorkerID
             JOIN shifts s ON a.ShiftID = s.ShiftID
             WHERE a.ShiftDate = ?
             ORDER BY s.StartTime ASC, w.LastName ASC",
            [$date]
        );
    }

    public function getByDateRange(string $from, string $to): array {
        return $this->query(
            "SELECT a.*, w.FirstName, w.LastName, w.Position, s.ShiftName, s.StartTime, s.EndTime
             FROM worker_shift_assignments a
             JOIN workers w ON a.WorkerID = w.WorkerID
             JOIN shifts s ON a.ShiftID = s.ShiftID
             WHERE a.ShiftDate BETWEEN ? AND ?
             ORDER BY a.ShiftDate ASC, s.StartTime ASC, w.LastName ASC",
            [$from, $to]
        );
    }

    public function getWorkerSchedule(string $workerId, string $from, string $to): array {
        return $this->query(
            "SELECT a.*, s.ShiftName, s.StartTime, s.EndTime
             FROM worker_shift_assignments a
             JOIN shifts s ON a.ShiftID = s.ShiftID
             WHERE a.WorkerID = ? AND a.ShiftDate BETWEEN ? AND ?
             ORDER BY a.ShiftDate ASC",
            [$workerId, $from, $to]
        );
    }

    public function create(array $data): string {
        $id = generateId('WSA');
        $stmt = $this->db->prepare(
            "INSERT INTO worker_shift_assignments (AssignmentID, WorkerID, ShiftID, ShiftDate, Status)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $id,
            $data['WorkerID'],
            $data['ShiftID'],
            $data['ShiftDate'],
            $data['Status'] ?? 'Scheduled',
        ]);
        return $id;
    }

    public function updateAssignment(string $id, array $data): bool {
        return $this->update($id, $data);
    }

    public function deleteAssignment(string $id): bool {
        return $this->delete($id);
    }

    public function countByDate(string $date): array {
        return $this->query(
            "SELECT s.ShiftName, COUNT(*) AS WorkerCount
             FROM worker_shift_assignments a
             JOIN shifts s ON a.ShiftID = s.ShiftID
             WHERE a.ShiftDate = ?
             GROUP BY s.ShiftName",
            [$date]
        );
    }

    public function getTodaySummary(): array {
        $today = date('Y-m-d');
        $rows = $this->countByDate($today);
        $result = ['Morning' => 0, 'Afternoon' => 0, 'Night' => 0, 'total' => 0];
        foreach ($rows as $r) {
            $name = $r['ShiftName'];
            $result[$name] = (int)$r['WorkerCount'];
            $result['total'] += (int)$r['WorkerCount'];
        }
        return $result;
    }
}
