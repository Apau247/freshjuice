<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class AuditTrailModel extends Model
{
    protected string $table = 'audit_trail';
    protected string $primaryKey = 'AuditID';

    public function getRecent(int $limit = 50, string $module = '', string $action = ''): array
    {
        $sql = "SELECT at.*, u.Name AS UserName FROM audit_trail at LEFT JOIN users u ON at.UserID = u.UserID";
        $where = [];
        $params = [];
        if ($module) {
            $where[] = "at.Module = ?";
            $params[] = $module;
        }
        if ($action) {
            $where[] = "at.Action = ?";
            $params[] = $action;
        }
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY at.created_at DESC LIMIT ?";
        $params[] = $limit;
        return $this->query($sql, $params);
    }
}
