<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class TrainingModel extends Model
{
    protected string $table = 'training_records';
    protected string $primaryKey = 'TrainingID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT tr.*, s.FirstName, s.LastName, CONCAT(s.FirstName, ' ', s.LastName) AS StaffName
            FROM {$this->table} tr
            LEFT JOIN staff s ON tr.StaffID = s.StaffID
            ORDER BY tr.TrainingDate DESC
        ";
        return $this->query($sql);
    }

    public function getUpcoming(): array
    {
        $sql = "
            SELECT tr.*, CONCAT(s.FirstName, ' ', s.LastName) AS StaffName
            FROM {$this->table} tr
            LEFT JOIN staff s ON tr.StaffID = s.StaffID
            WHERE tr.TrainingDate >= CURDATE()
            ORDER BY tr.TrainingDate ASC
        ";
        return $this->query($sql);
    }

    public function getExpiringCerts(): array
    {
        $sql = "
            SELECT tr.*, CONCAT(s.FirstName, ' ', s.LastName) AS StaffName
            FROM {$this->table} tr
            LEFT JOIN staff s ON tr.StaffID = s.StaffID
            WHERE tr.ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            AND tr.Status = 'Completed'
            ORDER BY tr.ExpiryDate ASC
        ";
        return $this->query($sql);
    }

    public function getCompletionStats(): array
    {
        $sql = "
            SELECT
                SUM(CASE WHEN Status = 'Completed' THEN 1 ELSE 0 END) AS completed,
                SUM(CASE WHEN Status = 'Scheduled' THEN 1 ELSE 0 END) AS scheduled
            FROM {$this->table}
        ";
        $row = $this->queryOne($sql);
        return $row ? (array)$row : ['completed' => 0, 'scheduled' => 0];
    }
}