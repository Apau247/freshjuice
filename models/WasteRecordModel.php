<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class WasteRecordModel extends Model {
    protected string $table = 'waste_records';
    protected string $primaryKey = 'WasteID';

    public function getAllDetailed(): array {
        return $this->query(
            "SELECT wr.*, pb.BatchNumber, pb.Flavour, u.Name AS RecordedByName
             FROM waste_records wr
             LEFT JOIN production_batches pb ON wr.BatchID = pb.BatchID
             LEFT JOIN users u ON wr.RecordedBy = u.UserID
             ORDER BY wr.Date DESC"
        );
    }

    public function getTotalWaste(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(Quantity), 0) FROM waste_records");
    }

    public function getWasteByType(): array {
        return $this->query("SELECT WasteType, SUM(Quantity) AS Total FROM waste_records GROUP BY WasteType");
    }
}
