<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class RawMaterialModel extends Model {
    protected string $table = 'raw_materials';
    protected string $primaryKey = 'MaterialID';

    public function getAllWithSuppliers(): array {
        return $this->query(
            "SELECT rm.*, s.Name AS SupplierName
             FROM raw_materials rm LEFT JOIN suppliers s ON rm.SupplierID = s.SupplierID
             ORDER BY rm.MaterialID DESC"
        );
    }

    public function updateStock(string $id, float $qty): bool {
        return $this->db->prepare("UPDATE raw_materials SET CurrentStock = CurrentStock + ? WHERE MaterialID = ?")->execute([$qty, $id]);
    }

    public function getLowStock(): array {
        return $this->query("SELECT * FROM raw_materials WHERE CurrentStock <= MinStock ORDER BY CurrentStock ASC");
    }

    public function getTotalStock(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(CurrentStock), 0) FROM raw_materials");
    }
}
