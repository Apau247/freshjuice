<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class DrillModel extends Model
{
    protected string $table = 'emergency_drills';
    protected string $primaryKey = 'DrillID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT ed.*, u.Name AS ConductedByName
            FROM {$this->table} ed
            LEFT JOIN users u ON ed.ConductedBy = u.UserID
            ORDER BY ed.DrillDate DESC
        ";
        return $this->query($sql);
    }

    public function getUpcoming(): array
    {
        $sql = "
            SELECT ed.*, u.Name AS ConductedByName
            FROM {$this->table} ed
            LEFT JOIN users u ON ed.ConductedBy = u.UserID
            WHERE ed.DrillDate >= CURDATE()
            AND ed.Status = 'Scheduled'
            ORDER BY ed.DrillDate ASC
        ";
        return $this->query($sql);
    }
}