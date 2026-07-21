<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class FatModel extends Model
{
    protected string $table = 'fat_records';
    protected string $primaryKey = 'FAT_ID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT fr.*, m.Name AS MachineName, u.Name AS TestedByName
            FROM {$this->table} fr
            LEFT JOIN machines m ON fr.MachineID = m.MachineID
            LEFT JOIN users u ON fr.TestedBy = u.UserID
            ORDER BY fr.created_at DESC
        ";
        return $this->query($sql);
    }

    public function getPending(): array
    {
        $sql = "
            SELECT fr.*, m.Name AS MachineName, u.Name AS TestedByName
            FROM {$this->table} fr
            LEFT JOIN machines m ON fr.MachineID = m.MachineID
            LEFT JOIN users u ON fr.TestedBy = u.UserID
            WHERE fr.Status IN ('Pending', 'In Progress')
            ORDER BY fr.created_at DESC
        ";
        return $this->query($sql);
    }
}