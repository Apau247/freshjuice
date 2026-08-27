<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class PackagingMaterialModel extends Model {
    protected string $table = 'packaging_materials';
    protected string $primaryKey = 'PackageID';

    public function updateStock(string $id, float $qty): bool {
        return $this->db->prepare("UPDATE packaging_materials SET CurrentStock = CurrentStock + ? WHERE PackageID = ?")->execute([$qty, $id]);
    }

    /** Lock a packaging material row and return it for atomic check-and-deduct. */
    public function lockForStock(string $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM packaging_materials WHERE PackageID = ? FOR UPDATE");
        $stmt->execute([$id]);
        $r = $stmt->fetch();
        return $r ?: null;
    }

    public function getLowStock(): array {
        return $this->query("SELECT * FROM packaging_materials WHERE CurrentStock <= MinStock ORDER BY CurrentStock ASC");
    }

    public function getTotalStock(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(CurrentStock), 0) FROM packaging_materials");
    }
}
