<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class QualityInspectionModel extends Model {
    protected string $table = 'quality_inspections';
    protected string $primaryKey = 'InspectionID';

    public function getAllDetailed(): array {
        return $this->query(
            "SELECT qi.*, pb.BatchNumber, pb.Flavour, u.Name AS InspectorName
             FROM quality_inspections qi
             LEFT JOIN production_batches pb ON qi.BatchID = pb.BatchID
             LEFT JOIN users u ON qi.InspectorID = u.UserID
             ORDER BY qi.InspectionDate DESC"
        );
    }

    public function getStatusCounts(): array {
        $rows = $this->query("SELECT Result, COUNT(*) AS cnt FROM quality_inspections GROUP BY Result");
        $out = ['Pass' => 0, 'Fail' => 0, 'Pending' => 0];
        foreach ($rows as $r) $out[$r['Result']] = (int)$r['cnt'];
        return $out;
    }

    public function getTypeCounts(): array {
        return $this->query("SELECT InspectionType, COUNT(*) AS cnt FROM quality_inspections GROUP BY InspectionType");
    }
}
