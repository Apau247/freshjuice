<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class StaffModel extends Model {
    protected string $table = 'staff';
    protected string $primaryKey = 'StaffID';

    public function getAllDetailed(): array {
        return $this->query(
            "SELECT s.*, u.Name AS UserName, r.RoleName
             FROM staff s
             LEFT JOIN users u ON s.UserID = u.UserID
             LEFT JOIN roles r ON u.RoleID = r.RoleID
             ORDER BY s.StaffID"
        );
    }

    public function getShifts(): array {
        return $this->db->query("SELECT * FROM shifts ORDER BY StartTime")->fetchAll();
    }

    public function getAttendance(string $date): array {
        return $this->query(
            "SELECT a.*, s.FirstName, s.LastName, sh.ShiftName
             FROM attendance a
             JOIN staff s ON a.StaffID = s.StaffID
             LEFT JOIN shifts sh ON a.ShiftID = sh.ShiftID
             WHERE a.Date = ?
             ORDER BY s.FirstName",
            [$date]
        );
    }

    public function createShift(array $data): bool {
        $cols = implode(', ', array_keys($data));
        $phs = implode(', ', array_fill(0, count($data), '?'));
        return $this->db->prepare("INSERT INTO shifts ({$cols}) VALUES ({$phs})")->execute(array_values($data));
    }

    public function updateShift(string $id, array $data): bool {
        $sets = array_map(fn($c) => "{$c} = ?", array_keys($data));
        $params = array_values($data);
        $params[] = $id;
        return $this->db->prepare("UPDATE shifts SET " . implode(', ', $sets) . " WHERE ShiftID = ?")->execute($params);
    }

    public function deleteShift(string $id): bool {
        return $this->db->prepare("DELETE FROM shifts WHERE ShiftID = ?")->execute([$id]);
    }
}
