<?php declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class SupplierEvalModel extends Model
{
    protected string $table = 'supplier_evaluations';
    protected string $primaryKey = 'EvaluationID';

    public function getAllDetailed(): array
    {
        $sql = "
            SELECT se.*, s.SupplierName, u.FullName AS EvaluatedByName
            FROM {$this->table} se
            LEFT JOIN suppliers s ON se.SupplierID = s.SupplierID
            LEFT JOIN users u ON se.EvaluatedBy = u.UserID
            ORDER BY se.EvaluationDate DESC
        ";
        return $this->query($sql);
    }

    public function getLatestBySupplier(int $supplierId): ?array
    {
        $sql = "
            SELECT se.*, s.SupplierName, u.FullName AS EvaluatedByName
            FROM {$this->table} se
            LEFT JOIN suppliers s ON se.SupplierID = s.SupplierID
            LEFT JOIN users u ON se.EvaluatedBy = u.UserID
            WHERE se.SupplierID = :supplierId
            ORDER BY se.EvaluationDate DESC
            LIMIT 1
        ";
        return $this->queryOne($sql, ['supplierId' => $supplierId]);
    }
}