<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class FinishedGoodModel extends Model {
    protected string $table = 'finished_goods';
    protected string $primaryKey = 'FG_ID';

    public function getAllDetailed(): array {
        return $this->query(
            "SELECT fg.*, pb.BatchNumber
             FROM finished_goods fg
             LEFT JOIN production_batches pb ON fg.BatchID = pb.BatchID
             ORDER BY fg.FG_ID DESC"
        );
    }

    public function getTotalAvailable(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(QuantityAvailable), 0) FROM finished_goods");
    }

    public function reduceStock(string $id, float $qty): bool {
        return $this->db->prepare("UPDATE finished_goods SET QuantityAvailable = QuantityAvailable - ? WHERE FG_ID = ? AND QuantityAvailable >= ?")->execute([$qty, $id, $qty]);
    }

    public function getExpiringSoon(int $days = 30): array {
        return $this->query("SELECT * FROM finished_goods WHERE ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL ? DAY) AND QuantityAvailable > 0 ORDER BY ExpiryDate ASC", [$days]);
    }
}
