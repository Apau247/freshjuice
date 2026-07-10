<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class SalesOrderModel extends Model {
    protected string $table = 'sales_orders';
    protected string $primaryKey = 'OrderID';

    public function getAllDetailed(): array {
        return $this->query(
            "SELECT so.*, c.Name AS CustomerName, fg.Flavour AS FG_Flavour, u.Name AS CreatedByName
             FROM sales_orders so
             LEFT JOIN customers c ON so.CustomerID = c.CustomerID
             LEFT JOIN finished_goods fg ON so.FG_ID = fg.FG_ID
             LEFT JOIN users u ON so.CreatedBy = u.UserID
             ORDER BY so.OrderDate DESC"
        );
    }

    public function getPendingCount(): int {
        return (int) $this->queryScalar("SELECT COUNT(*) FROM sales_orders WHERE Status IN ('Pending','Processing')");
    }

    public function getTotalRevenue(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(TotalAmount), 0) FROM sales_orders WHERE Status = 'Completed'");
    }
}
