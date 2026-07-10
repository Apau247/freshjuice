<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class DocumentModel extends Model
{
    protected string $table = 'documents';
    protected string $primaryKey = 'DocID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT d.*, u.Name AS ApprovedByName
            FROM {$this->table} d
            LEFT JOIN users u ON d.ApprovedBy = u.UserID
            ORDER BY d.created_at DESC
        ";
        return $this->query($sql);
    }

    public function getRecent(int $limit = 10): array
    {
        $sql = "
            SELECT d.*, u.Name AS ApprovedByName
            FROM {$this->table} d
            LEFT JOIN users u ON d.ApprovedBy = u.UserID
            ORDER BY d.created_at DESC
            LIMIT :limit
        ";
        return $this->query($sql, ['limit' => $limit]);
    }

    public function getUnderReview(): array
    {
        $sql = "
            SELECT d.*, u.Name AS ApprovedByName
            FROM {$this->table} d
            LEFT JOIN users u ON d.ApprovedBy = u.UserID
            WHERE d.Status = 'Under Review'
            ORDER BY d.created_at DESC
        ";
        return $this->query($sql);
    }
}