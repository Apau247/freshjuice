<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class PermitModel extends Model
{
    protected string $table = 'permits';
    protected string $primaryKey = 'PermitID';

    public function getExpiringSoon(int $days = 90): array
    {
        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL :days DAY)
            AND Status = 'Active'
            ORDER BY ExpiryDate ASC
        ";
        return $this->query($sql, ['days' => $days]);
    }

    public function getActiveCount(): int
    {
        $sql = "
            SELECT COUNT(*) AS cnt
            FROM {$this->table}
            WHERE Status = 'Active'
        ";
        $result = $this->queryScalar($sql);
        return (int)$result;
    }
}