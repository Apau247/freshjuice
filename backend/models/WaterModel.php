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

    public function findUsage(string $id): ?array {
        return $this->queryOne("SELECT * FROM water_usage WHERE WaterUsageID = ?", [$id]);
    }

    public function updateUsage(string $id, array $data): bool {
        $sets = implode(', ', array_map(fn($c) => "{$c} = ?", array_keys($data)));
        $params = array_values($data);
        $params[] = $id;
        return $this->db->prepare("UPDATE water_usage SET {$sets} WHERE WaterUsageID = ?")->execute($params);
    }

    public function deleteUsage(string $id): bool {
        return $this->db->prepare("DELETE FROM water_usage WHERE WaterUsageID = ?")->execute([$id]);
    }

    public function findTest(string $id): ?array {
        return $this->queryOne("SELECT * FROM water_quality_tests WHERE WaterTestID = ?", [$id]);
    }

    public function updateTest(string $id, array $data): bool {
        $sets = implode(', ', array_map(fn($c) => "{$c} = ?", array_keys($data)));
        $params = array_values($data);
        $params[] = $id;
        return $this->db->prepare("UPDATE water_quality_tests SET {$sets} WHERE WaterTestID = ?")->execute($params);
    }

    public function deleteTest(string $id): bool {
        return $this->db->prepare("DELETE FROM water_quality_tests WHERE WaterTestID = ?")->execute([$id]);
    }
}
