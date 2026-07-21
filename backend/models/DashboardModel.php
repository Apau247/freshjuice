<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

class DashboardModel extends Model {

    public function getStats(): array {
        $db = $this->db;

        $rawStock      = (float) $db->query("SELECT COALESCE(SUM(CurrentStock),0) FROM raw_materials")->fetchColumn();
        $rmCount       = (int)   $db->query("SELECT COUNT(*) FROM raw_materials")->fetchColumn();
        $pkgStock      = (float) $db->query("SELECT COALESCE(SUM(CurrentStock),0) FROM packaging_materials")->fetchColumn();
        $activeBatches = (int)   $db->query("SELECT COUNT(*) FROM production_batches WHERE Status IN ('Pending','In Progress')")->fetchColumn();
        $completedBat  = (int)   $db->query("SELECT COUNT(*) FROM production_batches WHERE Status='Completed'")->fetchColumn();
        $totalFG       = (float) $db->query("SELECT COALESCE(SUM(QuantityAvailable),0) FROM finished_goods")->fetchColumn();
        $pendingOrders = (int)   $db->query("SELECT COUNT(*) FROM sales_orders WHERE Status IN ('Pending','Processing')")->fetchColumn();
        $totalRevenue  = (float) $db->query("SELECT COALESCE(SUM(TotalAmount),0) FROM sales_orders WHERE Status='Completed'")->fetchColumn();
        $totalWaste    = (float) $db->query("SELECT COALESCE(SUM(Quantity),0) FROM waste_records")->fetchColumn();
        $totalProd     = (float) $db->query("SELECT COALESCE(SUM(Quantity),0) FROM production_batches WHERE Status='Completed'")->fetchColumn();
        $unpaidInv     = (float) $db->query("SELECT COALESCE(SUM(TotalDue),0) FROM invoices WHERE PaymentStatus IN ('Unpaid','Partial')")->fetchColumn();
        $pendingQI     = (int)   $db->query("SELECT COUNT(*) FROM quality_inspections WHERE Result='Pending'")->fetchColumn();
        $mTotal        = (int)   $db->query("SELECT COUNT(*) FROM machines")->fetchColumn();
        $mDown         = (int)   $db->query("SELECT COUNT(*) FROM machines WHERE Status IN ('Maintenance','Down')")->fetchColumn();
        $totalDowntime = (float) $db->query("SELECT COALESCE(SUM(Downtime),0) FROM maintenance_records")->fetchColumn();
        $waterTotal    = (float) $db->query("SELECT COALESCE(SUM(Quantity),0) FROM water_usage")->fetchColumn();
        $powerTotal    = (float) $db->query("SELECT COALESCE(SUM(ConsumptionKWh),0) FROM power_usage")->fetchColumn();
        $certsActive   = (int)   $db->query("SELECT COUNT(*) FROM certifications WHERE Status='Active'")->fetchColumn();
        $certsExpiring = (int)   $db->query("SELECT COUNT(*) FROM certifications WHERE ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 90 DAY) AND Status != 'Expired'")->fetchColumn();
        $totalSuppliers = (int)  $db->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();

        // Safety & Compliance
        $safetyOpen     = (int)   @$db->query("SELECT COUNT(*) FROM safety_inspections WHERE Status='Open'")->fetchColumn() ?: 0;
        $safetyClosed   = (int)   @$db->query("SELECT COUNT(*) FROM safety_inspections WHERE Status='Closed'")->fetchColumn() ?: 0;
        $accidentsOpen  = (int)   @$db->query("SELECT COUNT(*) FROM accident_reports WHERE Status IN ('Open','In Progress')")->fetchColumn() ?: 0;
        $hazardsHigh    = (int)   @$db->query("SELECT COUNT(*) FROM hazard_register WHERE RiskRating >= 12 AND Status='Active'")->fetchColumn() ?: 0;
        $permitsExpiring= (int)   @$db->query("SELECT COUNT(*) FROM permits WHERE ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 90 DAY) AND Status='Active'")->fetchColumn() ?: 0;
        $trainingPending= (int)   @$db->query("SELECT COUNT(*) FROM training_records WHERE Status='Scheduled'")->fetchColumn() ?: 0;
        $capaOpen       = (int)   @$db->query("SELECT COUNT(*) FROM improvement_initiatives WHERE Status IN ('Proposed','In Progress')")->fetchColumn() ?: 0;
        $capaOverdue    = (int)   @$db->query("SELECT COUNT(*) FROM improvement_initiatives WHERE TargetDate < CURDATE() AND Status != 'Completed'")->fetchColumn() ?: 0;
        $avgOEE         = (float) @$db->query("SELECT COALESCE(AVG(OEE),0) FROM production_efficiency WHERE Date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)")->fetchColumn() ?: 0;
        $ppeReplace     = (int)   @$db->query("SELECT COUNT(*) FROM ppe_records WHERE ReplacementNeeded=1")->fetchColumn() ?: 0;

        $wastePct = $totalProd > 0 ? round(($totalWaste / $totalProd) * 100, 2) : 0;

        return compact(
            'rawStock','rmCount','pkgStock','activeBatches','completedBat','totalFG',
            'pendingOrders','totalRevenue','totalWaste','totalProd','wastePct','unpaidInv',
            'pendingQI','mTotal','mDown','totalDowntime','waterTotal','powerTotal',
            'certsActive','certsExpiring','totalSuppliers',
            'safetyOpen','safetyClosed','accidentsOpen','hazardsHigh','permitsExpiring',
            'trainingPending','capaOpen','capaOverdue','avgOEE','ppeReplace'
        );
    }

    public function getProductionByFlavour(): array {
        return $this->db->query(
            "SELECT Flavour, SUM(Quantity) AS Total
             FROM production_batches WHERE Status='Completed'
             GROUP BY Flavour ORDER BY Total DESC"
        )->fetchAll();
    }

    public function getMonthlyProduction(): array {
        return $this->db->query(
            "SELECT DATE_FORMAT(ProductionDate, '%Y-%m') AS Month, SUM(Quantity) AS Total
             FROM production_batches WHERE Status='Completed'
             GROUP BY Month ORDER BY Month DESC LIMIT 12"
        )->fetchAll();
    }

    public function getMonthlySales(): array {
        return $this->db->query(
            "SELECT DATE_FORMAT(OrderDate, '%Y-%m') AS Month, SUM(TotalAmount) AS Total
             FROM sales_orders WHERE Status='Completed'
             GROUP BY Month ORDER BY Month DESC LIMIT 12"
        )->fetchAll();
    }

    public function getRecentBatches(int $limit = 5): array {
        return $this->query("SELECT * FROM production_batches ORDER BY ProductionDate DESC LIMIT ?", [$limit]);
    }

    public function getRecentOrders(int $limit = 5): array {
        return $this->query(
            "SELECT so.*, c.Name AS CustomerName FROM sales_orders so
             LEFT JOIN customers c ON so.CustomerID = c.CustomerID
             ORDER BY so.OrderDate DESC LIMIT ?", [$limit]
        );
    }

    public function getOEEByMachine(): array {
        return $this->db->query(
            "SELECT m.Name AS MachineName, AVG(p.AvailabilityRate) AS Availability,
                    AVG(p.PerformanceRate) AS Performance, AVG(p.QualityRate) AS Quality, AVG(p.OEE) AS AvgOEE
             FROM production_efficiency p
             JOIN machines m ON p.MachineID = m.MachineID
             GROUP BY m.Name ORDER BY AvgOEE DESC"
        )->fetchAll();
    }

    public function getWasteByType(): array {
        return $this->db->query(
            "SELECT WasteType, SUM(Quantity) AS Total FROM waste_records
             GROUP BY WasteType ORDER BY Total DESC"
        )->fetchAll();
    }

    public function getMonthlyRevenue(): array {
        return $this->db->query(
            "SELECT DATE_FORMAT(OrderDate, '%Y-%m') AS Month, SUM(TotalAmount) AS Total
             FROM sales_orders WHERE Status='Completed'
             GROUP BY Month ORDER BY Month DESC LIMIT 12"
        )->fetchAll();
    }

    public function getRecentSafetyInspections(int $limit = 5): array {
        return $this->query(
            "SELECT s.*, u.Name AS InspectorName FROM safety_inspections s
             LEFT JOIN users u ON s.InspectorID = u.UserID
             ORDER BY s.InspectionDate DESC LIMIT ?", [$limit]
        );
    }

    public function getRecentImprovements(int $limit = 5): array {
        return $this->query(
            "SELECT * FROM improvement_initiatives ORDER BY TargetDate ASC LIMIT ?", [$limit]
        );
    }
}
