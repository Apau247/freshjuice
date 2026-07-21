<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class InvoiceModel extends Model {
    protected string $table = 'invoices';
    protected string $primaryKey = 'InvoiceID';

    public function getAllDetailed(): array {
        return $this->query(
            "SELECT i.*, so.OrderDate, c.Name AS CustomerName
             FROM invoices i
             LEFT JOIN sales_orders so ON i.OrderID = so.OrderID
             LEFT JOIN customers c ON so.CustomerID = c.CustomerID
             ORDER BY i.InvoiceDate DESC"
        );
    }

    public function getUnpaidTotal(): float {
        return (float) $this->queryScalar("SELECT COALESCE(SUM(TotalDue), 0) FROM invoices WHERE PaymentStatus IN ('Unpaid','Partial','Overdue')");
    }

    public function getMonthlyRevenue(string $month): float {
        return (float) $this->queryScalar(
            "SELECT COALESCE(SUM(Amount), 0) FROM invoices WHERE DATE_FORMAT(InvoiceDate, '%Y-%m') = ? AND PaymentStatus = 'Paid'",
            [$month]
        );
    }
}
