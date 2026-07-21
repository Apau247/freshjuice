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
}
