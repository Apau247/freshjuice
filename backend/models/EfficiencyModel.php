<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class EfficiencyModel extends Model
{
    protected string $table = 'production_efficiency';
    protected string $primaryKey = 'EfficiencyID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT pe.*, m.Name AS MachineName
            FROM {$this->table} pe
            LEFT JOIN machines m ON pe.MachineID = m.MachineID
            ORDER BY pe.Date DESC
        ";
        return $this->query($sql);
    }

    public function getAverageOEE(?string $startDate = null, ?string $endDate = null): ?float
    {
        $sql = "SELECT AVG(OEE) AS avgOEE FROM {$this->table}";
        $params = [];
        $conditions = [];

        if ($startDate !== null) {
            $conditions[] = 'Date >= :startDate';
            $params['startDate'] = $startDate;
        }
        if ($endDate !== null) {
            $conditions[] = 'Date <= :endDate';
            $params['endDate'] = $endDate;
        }

        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $result = $this->queryScalar($sql, $params);
        return $result !== null ? (float)$result : null;
    }

    public function getDailyOEE(string $date): array
    {
        $sql = "
            SELECT pe.*, m.Name AS MachineName
            FROM {$this->table} pe
            LEFT JOIN machines m ON pe.MachineID = m.MachineID
            WHERE pe.Date = :date
            ORDER BY m.Name
        ";
        return $this->query($sql, ['date' => $date]);
    }

    public function getMonthlyOEE(): array
    {
        $sql = "
            SELECT
                DATE_FORMAT(Date, '%Y-%m') AS Month,
                AVG(OEE) AS avgOEE,
                AVG(AvailabilityRate) AS avgAvailability,
                AVG(PerformanceRate) AS avgPerformance,
                AVG(QualityRate) AS avgQuality
            FROM {$this->table}
            GROUP BY DATE_FORMAT(Date, '%Y-%m')
            ORDER BY Month DESC
        ";
        return $this->query($sql);
    }
}