<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class PpeModel extends Model
{
    protected string $table = 'ppe_records';
    protected string $primaryKey = 'PPE_ID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT pr.*, CONCAT(s.FirstName, ' ', s.LastName) AS StaffName
            FROM {$this->table} pr
            LEFT JOIN staff s ON pr.StaffID = s.StaffID
            ORDER BY pr.IssueDate DESC
        ";
        return $this->query($sql);
    }

    public function getExpiringSoon(): array
    {
        $sql = "
            SELECT pr.*, CONCAT(s.FirstName, ' ', s.LastName) AS StaffName
            FROM {$this->table} pr
            LEFT JOIN staff s ON pr.StaffID = s.StaffID
            WHERE pr.ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
            ORDER BY pr.ExpiryDate ASC
        ";
        return $this->query($sql);
    }

    public function getReplacementNeeded(): array
    {
        $sql = "
            SELECT pr.*, CONCAT(s.FirstName, ' ', s.LastName) AS StaffName
            FROM {$this->table} pr
            LEFT JOIN staff s ON pr.StaffID = s.StaffID
            WHERE pr.ReplacementNeeded = 1
            ORDER BY pr.ExpiryDate ASC
        ";
        return $this->query($sql);
    }
}