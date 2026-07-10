<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class AccidentModel extends Model
{
    protected string $table = 'accident_reports';
    protected string $primaryKey = 'AccidentID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT ar.*, u.Name AS ReportedByName
            FROM {$this->table} ar
            LEFT JOIN users u ON ar.ReportedBy = u.UserID
            ORDER BY ar.created_at DESC
        ";
        return $this->query($sql);
    }

    public function getStats(): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Status = 'Open' THEN 1 ELSE 0 END) AS openCount,
                SUM(CASE WHEN Status = 'Closed' THEN 1 ELSE 0 END) AS closedCount
            FROM {$this->table}
        ";
        $row = $this->queryOne($sql);
        return $row ? (array)$row : ['total' => 0, 'openCount' => 0, 'closedCount' => 0];
    }
}