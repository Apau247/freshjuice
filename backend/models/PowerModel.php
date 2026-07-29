<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class PowerModel extends Model {

    public function getUsageRecords(): array {
        return $this->db->query("SELECT * FROM power_usage ORDER BY Date DESC")->fetchAll();
    }

    public function getGeneratorLogs(): array {
        return $this->db->query("SELECT * FROM generator_log ORDER BY Date DESC")->fetchAll();
    }

    public function getTotalConsumption(): float {
        return (float) $this->db->query("SELECT COALESCE(SUM(ConsumptionKWh), 0) FROM power_usage")->fetchColumn();
    }

    public function getTotalFuelUsed(): float {
        return (float) $this->db->query("SELECT COALESCE(SUM(FuelUsed), 0) FROM generator_log")->fetchColumn();
    }

    public function getTotalPowerCost(): float {
        return (float) $this->db->query("SELECT COALESCE(SUM(Cost), 0) FROM power_usage")->fetchColumn();
    }

    public function getMonthlyConsumption(): array {
        return $this->db->query("SELECT DATE_FORMAT(Date, '%Y-%m') AS Month, SUM(ConsumptionKWh) AS Total FROM power_usage GROUP BY Month ORDER BY Month DESC LIMIT 12")->fetchAll();
    }

    public function findUsage(string $id): ?array {
        return $this->queryOne("SELECT * FROM power_usage WHERE PowerUsageID = ?", [$id]);
    }

    public function updateUsage(string $id, array $data): bool {
        $sets = implode(', ', array_map(fn($c) => "{$c} = ?", array_keys($data)));
        $params = array_values($data);
        $params[] = $id;
        return $this->db->prepare("UPDATE power_usage SET {$sets} WHERE PowerUsageID = ?")->execute($params);
    }

    public function deleteUsage(string $id): bool {
        return $this->db->prepare("DELETE FROM power_usage WHERE PowerUsageID = ?")->execute([$id]);
    }

    public function findGeneratorLog(string $id): ?array {
        return $this->queryOne("SELECT * FROM generator_log WHERE LogID = ?", [$id]);
    }

    public function updateGeneratorLog(string $id, array $data): bool {
        $sets = implode(', ', array_map(fn($c) => "{$c} = ?", array_keys($data)));
        $params = array_values($data);
        $params[] = $id;
        return $this->db->prepare("UPDATE generator_log SET {$sets} WHERE LogID = ?")->execute($params);
    }

    public function deleteGeneratorLog(string $id): bool {
        return $this->db->prepare("DELETE FROM generator_log WHERE LogID = ?")->execute([$id]);
    }
}
