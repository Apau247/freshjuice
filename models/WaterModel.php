<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class WaterModel extends Model {

    public function getUsageRecords(): array {
        return $this->query(
            "SELECT wu.*, u.Name AS RecordedByName
             FROM water_usage wu
             LEFT JOIN users u ON wu.RecordedBy = u.UserID
             ORDER BY wu.Date DESC"
        );
    }

    public function getQualityTests(): array {
        return $this->query(
            "SELECT wqt.*, u.Name AS TestedByName
             FROM water_quality_tests wqt
             LEFT JOIN users u ON wqt.TestedBy = u.UserID
             ORDER BY wqt.TestDate DESC"
        );
    }

    public function getTotalUsage(): float {
        return (float) $this->db->query("SELECT COALESCE(SUM(Quantity), 0) FROM water_usage")->fetchColumn();
    }

    public function getUsageByType(): array {
        return $this->db->query("SELECT UsageType, SUM(Quantity) AS Total FROM water_usage GROUP BY UsageType")->fetchAll();
    }

    public function getMonthlyUsage(): array {
        return $this->db->query("SELECT DATE_FORMAT(Date, '%Y-%m') AS Month, SUM(Quantity) AS Total FROM water_usage GROUP BY Month ORDER BY Month DESC LIMIT 12")->fetchAll();
    }
}
