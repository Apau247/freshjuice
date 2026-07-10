<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class ImprovementModel extends Model
{
    protected string $table = 'improvement_initiatives';
    protected string $primaryKey = 'InitiativeID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT ii.*, u.Name AS CreatedByName, ii.ResponsiblePerson
            FROM {$this->table} ii
            LEFT JOIN users u ON ii.CreatedBy = u.UserID
            ORDER BY ii.created_at DESC
        ";
        return $this->query($sql);
    }

    public function getStatusCounts(): array
    {
        $sql = "
            SELECT Status, COUNT(*) AS Count
            FROM {$this->table}
            GROUP BY Status
            ORDER BY Status
        ";
        $rows = $this->query($sql);
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['Status']] = (int)$row['Count'];
        }
        return $counts;
    }

    public function getOverdue(): array
    {
        $sql = "
            SELECT ii.*, u.Name AS CreatedByName
            FROM {$this->table} ii
            LEFT JOIN users u ON ii.CreatedBy = u.UserID
            WHERE ii.TargetDate < CURDATE()
            AND ii.Status != 'Completed'
            ORDER BY ii.TargetDate ASC
        ";
        return $this->query($sql);
    }
}