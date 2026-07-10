<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class MaintenanceRecordModel extends Model {
    protected string $table = 'maintenance_records';
    protected string $primaryKey = 'MaintenanceID';

    public function getAllDetailed(): array {
        return $this->query(
            "SELECT mr.*, m.Name AS MachineName, u.Name AS TechnicianName
             FROM maintenance_records mr
             LEFT JOIN machines m ON mr.MachineID = m.MachineID
             LEFT JOIN users u ON mr.TechnicianID = u.UserID
             ORDER BY mr.MaintenanceDate DESC"
        );
    }

    public function getUpcoming(): array {
        return $this->query("SELECT * FROM maintenance_records WHERE NextServiceDate <= DATE_ADD(CURDATE(), INTERVAL 14 DAY) AND Status = 'Completed' ORDER BY NextServiceDate ASC");
    }

    public function getTotalDowntime(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(Downtime), 0) FROM maintenance_records");
    }

    public function getTotalCost(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(Cost), 0) FROM maintenance_records");
    }
}
