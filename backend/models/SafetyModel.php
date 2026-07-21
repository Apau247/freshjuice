<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class SafetyModel extends Model
{
    protected string $table = 'safety_inspections';
    protected string $primaryKey = 'SafetyID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT si.*, u.Name AS InspectorName
            FROM {$this->table} si
            LEFT JOIN users u ON si.InspectorID = u.UserID
            ORDER BY si.InspectionDate DESC
        ";
        return $this->query($sql);
    }

    public function getStatusCounts(): array
    {
        $sql = "
            SELECT
                SUM(CASE WHEN Status = 'Open' THEN 1 ELSE 0 END) AS Open,
                SUM(CASE WHEN Status = 'In Progress' THEN 1 ELSE 0 END) AS InProgress,
                SUM(CASE WHEN Status = 'Closed' THEN 1 ELSE 0 END) AS Closed
            FROM {$this->table}
        ";
        $row = $this->queryOne($sql);
        return $row ? (array)$row : ['Open' => 0, 'InProgress' => 0, 'Closed' => 0];
    }

    public function getHazardCounts(): array
    {
        $sql = "
            SELECT HazardLevel, COUNT(*) AS Count
            FROM {$this->table}
            GROUP BY HazardLevel
            ORDER BY HazardLevel
        ";
        $rows = $this->query($sql);
        $counts = [];
        foreach ($rows as $row) {
            $counts[$row['HazardLevel']] = (int)$row['Count'];
        }
        return $counts;
    }
}