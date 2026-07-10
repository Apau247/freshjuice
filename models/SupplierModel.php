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
}
