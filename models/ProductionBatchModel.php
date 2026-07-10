<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class ProductionBatchModel extends Model {
    protected string $table = 'production_batches';
    protected string $primaryKey = 'BatchID';

    public function getAllDetailed(): array {
        return $this->query(
            "SELECT pb.*, rm.Name AS RawMaterialName, pm.Name AS PackagingName, m.Name AS MachineName, u.Name AS UserName
             FROM production_batches pb
             LEFT JOIN raw_materials rm ON pb.RawMaterialID = rm.MaterialID
             LEFT JOIN packaging_materials pm ON pb.PackagingMaterialID = pm.PackageID
             LEFT JOIN machines m ON pb.MachineID = m.MachineID
             LEFT JOIN users u ON pb.UserID = u.UserID
             ORDER BY pb.ProductionDate DESC"
        );
    }

    public function getStatusCounts(): array {
        $rows = $this->query("SELECT Status, COUNT(*) AS cnt FROM production_batches GROUP BY Status");
        $out = ['Pending' => 0, 'In Progress' => 0, 'Completed' => 0, 'Rejected' => 0, 'Cancelled' => 0];
        foreach ($rows as $r) $out[$r['Status']] = (int)$r['cnt'];
        return $out;
    }

    public function getTotalProduction(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(Quantity), 0) FROM production_batches WHERE Status = 'Completed'");
    }
}
