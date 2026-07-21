<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class SupplierModel extends Model {
    protected string $table = 'suppliers';
    protected string $primaryKey = 'SupplierID';

    public function getAllDeliveries(): array {
        return $this->query(
            "SELECT sd.*, s.Name AS SupplierName, u.Name AS ReceivedByName
             FROM supplier_deliveries sd
             LEFT JOIN suppliers s ON sd.SupplierID = s.SupplierID
             LEFT JOIN users u ON sd.ReceivedBy = u.UserID
             ORDER BY sd.DeliveryDate DESC"
        );
    }

    public function getRecentDeliveries(int $limit = 5): array {
        return $this->query(
            "SELECT sd.*, s.Name AS SupplierName
             FROM supplier_deliveries sd
             LEFT JOIN suppliers s ON sd.SupplierID = s.SupplierID
             ORDER BY sd.DeliveryDate DESC LIMIT ?",
            [$limit]
        );
    }

    public function findDelivery(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM supplier_deliveries WHERE DeliveryID = ?");
        $stmt->execute([$id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public function createDelivery(array $data): string {
        $cols = implode(', ', array_keys($data));
        $phs = implode(', ', array_fill(0, count($data), '?'));
        $stmt = $this->db->prepare("INSERT INTO supplier_deliveries ({$cols}) VALUES ({$phs})");
        $stmt->execute(array_values($data));
        return $data['DeliveryID'] ?? '';
    }

    public function updateDelivery(string $id, array $data): bool {
        $sets = array_map(fn($c) => "{$c} = ?", array_keys($data));
        $params = array_values($data);
        $params[] = $id;
        $stmt = $this->db->prepare("UPDATE supplier_deliveries SET " . implode(', ', $sets) . " WHERE DeliveryID = ?");
        return $stmt->execute($params);
    }

    public function deleteDelivery(string $id): bool {
        return $this->db->prepare("DELETE FROM supplier_deliveries WHERE DeliveryID = ?")->execute([$id]);
    }
}
