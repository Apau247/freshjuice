<?php
declare(strict_types=1);
require_once __DIR__ . '/Model.php';

/**
 * Builds every SRS report as a generic dataset:
 *   ['title', 'period', 'summary' => [label => value], 'headers' => [], 'rows' => []]
 * One renderer + one CSV exporter serve all report types.
 */
class ReportModel extends Model
{
    public const TYPES = [
        'production'    => 'Production Report',
        'inventory'     => 'Inventory Report',
        'sales'         => 'Sales Report',
        'supplier'      => 'Supplier Report',
        'waste'         => 'Waste Report',
        'water'         => 'Water Report',
        'maintenance'   => 'Maintenance Report',
        'downtime'      => 'Downtime Report',
        'power'         => 'Power Report',
        'packaging'     => 'Packaging Report',
        'certification' => 'Certification Report',
        'qaqc'          => 'QA/QC Report',
        'sop'           => 'SOP Compliance Report',
        'profit-loss'   => 'Profit & Loss Report',
        'oee'           => 'OEE Report',
    ];

    /** Each report declares which RBAC module gates it. */
    private const MODULE_OF = [
        'production'    => 'production',
        'inventory'     => 'materials',
        'sales'         => 'sales',
        'supplier'      => 'suppliers',
        'waste'         => 'waste',
        'water'         => 'water',
        'maintenance'   => 'maintenance',
        'downtime'      => 'maintenance',
        'power'         => 'power',
        'packaging'     => 'materials',
        'certification' => 'certifications',
        'qaqc'          => 'quality',
        'sop'           => 'sops',
        'profit-loss'   => 'invoicing',
        'oee'           => 'efficiency',
    ];

    public static function moduleFor(string $type): ?string
    {
        return self::MODULE_OF[$type] ?? null;
    }

    public function build(string $type, string $from, string $to): array
    {
        return match ($type) {
            'production'    => $this->production($from, $to),
            'inventory'     => $this->inventory(),
            'sales'         => $this->sales($from, $to),
            'supplier'      => $this->supplier($from, $to),
            'waste'         => $this->waste($from, $to),
            'water'         => $this->water($from, $to),
            'maintenance'   => $this->maintenance($from, $to),
            'downtime'      => $this->downtime($from, $to),
            'power'         => $this->power($from, $to),
            'packaging'     => $this->packaging($from, $to),
            'certification' => $this->certification(),
            'qaqc'          => $this->qaqc($from, $to),
            'sop'           => $this->sop($from, $to),
            'profit-loss'   => $this->profitLoss($from, $to),
            'oee'           => $this->oee($from, $to),
            default         => ['title' => 'Unknown Report', 'period' => '', 'summary' => [], 'headers' => [], 'rows' => []],
        };
    }

    private function fmt(string $date): string
    {
        return date('d M Y', strtotime($date));
    }

    private function production(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT b.BatchNumber, b.ProductionDate, b.Flavour, b.Quantity, b.Unit,
                    b.Status, m.Name AS MachineName, u.Name AS ProducedBy,
                    rm.Name AS RawMaterial
             FROM production_batches b
             LEFT JOIN machines m ON b.MachineID = m.MachineID
             LEFT JOIN users u ON b.UserID = u.UserID
             LEFT JOIN raw_materials rm ON b.RawMaterialID = rm.MaterialID
             WHERE b.ProductionDate BETWEEN ? AND ?
             ORDER BY b.ProductionDate DESC",
            [$from, $to]
        );
        $completed = array_filter($rows, fn($r) => $r['Status'] === 'Completed');
        $qty = array_sum(array_map(fn($r) => (float)$r['Quantity'], $completed));
        return [
            'title' => self::TYPES['production'],
            'period' => "$from to $to",
            'summary' => [
                'Total Batches'   => count($rows),
                'Completed'       => count($completed),
                'Quantity Produced' => number_format($qty, 2),
            ],
            'headers' => ['Batch Number', 'Date', 'Flavour', 'Quantity', 'Unit', 'Raw Material', 'Machine', 'Produced By', 'Status'],
            'rows'    => array_map(fn($r) => [
                $r['BatchNumber'], $this->fmt($r['ProductionDate']), $r['Flavour'],
                number_format((float)$r['Quantity'], 2), $r['Unit'],
                $r['RawMaterial'] ?? '-', $r['MachineName'] ?? '-', $r['ProducedBy'] ?? '-',
                $r['Status'],
            ], $rows),
        ];
    }

    private function inventory(): array
    {
        $raw = $this->query(
            "SELECT MaterialID AS ID, Name, Type, CurrentStock, MinStock, Unit, 'Raw Material' AS Category
             FROM raw_materials WHERE Status='Active'
             UNION ALL
             SELECT PackageID, Name, Type, CurrentStock, MinStock, Unit, 'Packaging'
             FROM packaging_materials WHERE Status='Active'
             ORDER BY Category, Name"
        );
        $goods = $this->query(
            "SELECT fg.FG_ID, fg.Flavour, fg.ExpiryDate, fg.QuantityAvailable, fg.Unit,
                    fg.StorageLocation, b.BatchNumber
             FROM finished_goods fg
             LEFT JOIN production_batches b ON fg.BatchID = b.BatchID
             WHERE fg.QuantityAvailable > 0 ORDER BY fg.ExpiryDate ASC"
        );
        $rawValue = count($raw);
        $fgQty = array_sum(array_map(fn($g) => (float)$g['QuantityAvailable'], $goods));
        return [
            'title' => self::TYPES['inventory'],
            'period' => 'Snapshot as of ' . date('d M Y'),
            'summary' => [
                'Materials Tracked'    => $rawValue,
                'Finished Goods Lines' => count($goods),
                'Finished Goods Qty'   => number_format($fgQty, 2),
            ],
            'headers' => ['Category', 'ID', 'Name / Flavour', 'Type', 'Stock', 'Unit', 'Min Stock', 'Extra'],
            'rows'    => array_merge(
                array_map(fn($r) => [
                    $r['Category'], $r['ID'], $r['Name'], $r['Type'] ?? '-',
                    number_format((float)$r['CurrentStock'], 2), $r['Unit'],
                    number_format((float)$r['MinStock'], 2),
                    ((float)$r['CurrentStock'] <= (float)$r['MinStock']) ? 'LOW STOCK' : '',
                ], $raw),
                array_map(fn($g) => [
                    'Finished Good', $g['FG_ID'], $g['Flavour'], '-',
                    number_format((float)$g['QuantityAvailable'], 2), $g['Unit'], '-',
                    'Exp ' . $this->fmt($g['ExpiryDate']) . ($g['BatchNumber'] ? ' (Batch ' . $g['BatchNumber'] . ')' : ''),
                ], $goods)
            ),
        ];
    }

    private function sales(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT so.OrderID, so.OrderDate, c.Name AS Customer, so.Quantity,
                    so.TotalAmount, so.Status, fg.Flavour, i.PaymentStatus
             FROM sales_orders so
             LEFT JOIN customers c ON so.CustomerID = c.CustomerID
             LEFT JOIN finished_goods fg ON so.FG_ID = fg.FG_ID
             LEFT JOIN invoices i ON i.OrderID = so.OrderID
             WHERE so.OrderDate BETWEEN ? AND ?
             ORDER BY so.OrderDate DESC",
            [$from, $to]
        );
        $revenue = array_sum(array_map(fn($r) => $r['Status'] === 'Completed' ? (float)$r['TotalAmount'] : 0, $rows));
        return [
            'title' => self::TYPES['sales'],
            'period' => "$from to $to",
            'summary' => [
                'Orders'        => count($rows),
                'Revenue (Completed)' => number_format($revenue, 2),
            ],
            'headers' => ['Order ID', 'Date', 'Customer', 'Product', 'Quantity', 'Total Amount', 'Order Status', 'Payment'],
            'rows'    => array_map(fn($r) => [
                $r['OrderID'], $this->fmt($r['OrderDate']), $r['Customer'] ?? '-',
                $r['Flavour'] ?? '-', number_format((float)$r['Quantity'], 2),
                number_format((float)$r['TotalAmount'], 2), $r['Status'],
                $r['PaymentStatus'] ?? '-',
            ], $rows),
        ];
    }

    private function supplier(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT s.SupplierID, s.Name, s.Contact, s.Phone,
                    COUNT(d.DeliveryID) AS Deliveries,
                    COALESCE(SUM(CASE WHEN d.Status='Received' THEN d.Quantity ELSE 0 END),0) AS QtyReceived,
                    SUM(d.Status='Rejected') AS Rejected
             FROM suppliers s
             LEFT JOIN supplier_deliveries d
                    ON d.SupplierID = s.SupplierID
                   AND d.DeliveryDate BETWEEN ? AND ?
             GROUP BY s.SupplierID, s.Name, s.Contact, s.Phone
             ORDER BY QtyReceived DESC",
            [$from, $to]
        );
        return [
            'title' => self::TYPES['supplier'],
            'period' => "$from to $to",
            'summary' => [
                'Suppliers'         => count($rows),
                'Total Deliveries'  => array_sum(array_map(fn($r) => (int)$r['Deliveries'], $rows)),
            ],
            'headers' => ['Supplier ID', 'Supplier', 'Contact', 'Phone', 'Deliveries', 'Qty Received', 'Rejected'],
            'rows'    => array_map(fn($r) => [
                $r['SupplierID'], $r['Name'], $r['Contact'] ?? '-', $r['Phone'] ?? '-',
                (string)(int)$r['Deliveries'], number_format((float)$r['QtyReceived'], 2),
                (string)(int)$r['Rejected'],
            ], $rows),
        ];
    }

    private function waste(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT w.Date, w.WasteType, w.Quantity, w.Unit, w.DisposalMethod,
                    w.EnvironmentalImpact, b.BatchNumber
             FROM waste_records w
             LEFT JOIN production_batches b ON w.BatchID = b.BatchID
             WHERE w.Date BETWEEN ? AND ?
             ORDER BY w.Date DESC",
            [$from, $to]
        );
        $totalQ = array_sum(array_map(fn($r) => (float)$r['Quantity'], $rows));
        $prod = (float)$this->queryScalar(
            "SELECT COALESCE(SUM(Quantity),0) FROM production_batches
             WHERE Status='Completed' AND ProductionDate BETWEEN ? AND ?",
            [$from, $to]
        );
        return [
            'title' => self::TYPES['waste'],
            'period' => "$from to $to",
            'summary' => [
                'Records'          => count($rows),
                'Total Waste'      => number_format($totalQ, 2),
                'Waste vs Production' => $prod > 0 ? number_format($totalQ / $prod * 100, 2) . '%' : '-',
            ],
            'headers' => ['Date', 'Batch', 'Waste Type', 'Quantity', 'Unit', 'Disposal Method', 'Environmental Impact'],
            'rows'    => array_map(fn($r) => [
                $this->fmt($r['Date']), $r['BatchNumber'] ?? '-', $r['WasteType'],
                number_format((float)$r['Quantity'], 2), $r['Unit'],
                $r['DisposalMethod'], $r['EnvironmentalImpact'] ?? '-',
            ], $rows),
        ];
    }

    private function water(string $from, string $to): array
    {
        $usage = $this->query(
            "SELECT Date, UsageType, Quantity, Unit, Purpose
             FROM water_usage WHERE Date BETWEEN ? AND ? ORDER BY Date DESC",
            [$from, $to]
        );
        $tests = $this->query(
            "SELECT TestDate, TestType, pH_Level, Turbidity, TDS, Chlorine, Result
             FROM water_quality_tests WHERE TestDate BETWEEN ? AND ? ORDER BY TestDate DESC",
            [$from, $to]
        );
        $byType = [];
        foreach ($usage as $u) {
            $byType[$u['UsageType']] = ($byType[$u['UsageType']] ?? 0) + (float)$u['Quantity'];
        }
        $passed = count(array_filter($tests, fn($t) => $t['Result'] === 'Pass'));
        $rows = array_merge(
            array_map(fn($u) => ['Usage', $this->fmt($u['Date']), $u['UsageType'],
                number_format((float)$u['Quantity'], 2) . ' ' . $u['Unit'], $u['Purpose'] ?? '-'], $usage),
            array_map(fn($t) => ['Quality Test', $this->fmt($t['TestDate']), $t['TestType'],
                'pH ' . ($t['pH_Level'] ?? '-') . ' / TDS ' . ($t['TDS'] ?? '-'), $t['Result']], $tests)
        );
        return [
            'title' => self::TYPES['water'],
            'period' => "$from to $to",
            'summary' => [
                'Consumption By Use' => implode(', ', array_map(
                    fn($k, $v) => "$k: " . number_format($v, 1), array_keys($byType), $byType)) ?: '-',
                'Tests Passed' => $passed . '/' . count($tests),
            ],
            'headers' => ['Record', 'Date', 'Type', 'Value', 'Detail'],
            'rows'    => $rows,
        ];
    }

    private function maintenance(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT r.MaintenanceID, r.MaintenanceDate, r.MaintenanceType, m.Name AS Machine,
                    r.Description, r.Cost, r.Downtime, r.NextServiceDate, r.Status, u.Name AS Technician
             FROM maintenance_records r
             LEFT JOIN machines m ON r.MachineID = m.MachineID
             LEFT JOIN users u ON r.TechnicianID = u.UserID
             WHERE r.MaintenanceDate BETWEEN ? AND ?
             ORDER BY r.MaintenanceDate DESC",
            [$from, $to]
        );
        $cost = array_sum(array_map(fn($r) => (float)$r['Cost'], $rows));
        $due = (int)$this->queryScalar(
            "SELECT COUNT(*) FROM maintenance_records
             WHERE Status='Scheduled' AND MaintenanceDate <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)"
        );
        return [
            'title' => self::TYPES['maintenance'],
            'period' => "$from to $to",
            'summary' => [
                'Records'        => count($rows),
                'Total Cost'     => number_format($cost, 2),
                'Reminders Due (7d)' => $due,
            ],
            'headers' => ['ID', 'Date', 'Machine', 'Type', 'Technician', 'Cost', 'Downtime (hrs)', 'Next Service', 'Status'],
            'rows'    => array_map(fn($r) => [
                $r['MaintenanceID'], $this->fmt($r['MaintenanceDate']), $r['Machine'] ?? '-',
                $r['MaintenanceType'], $r['Technician'] ?? '-',
                number_format((float)$r['Cost'], 2), number_format((float)$r['Downtime'], 2),
                $r['NextServiceDate'] ? $this->fmt($r['NextServiceDate']) : '-', $r['Status'],
            ], $rows),
        ];
    }

    private function downtime(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT m.Name AS Machine, m.Type,
                    COUNT(r.MaintenanceID) AS Events,
                    COALESCE(SUM(r.Downtime),0) AS DowntimeHours,
                    COALESCE(SUM(r.Cost),0) AS Cost
             FROM maintenance_records r
             JOIN machines m ON r.MachineID = m.MachineID
             WHERE r.MaintenanceDate BETWEEN ? AND ? AND r.Status != 'Cancelled'
             GROUP BY m.Name, m.Type
             ORDER BY DowntimeHours DESC",
            [$from, $to]
        );
        $machinesDown = (int)$this->queryScalar("SELECT COUNT(*) FROM machines WHERE Status IN ('Maintenance','Down')");
        return [
            'title' => self::TYPES['downtime'],
            'period' => "$from to $to",
            'summary' => [
                'Machines Affected' => count($rows),
                'Machines Currently Down' => $machinesDown,
            ],
            'headers' => ['Machine', 'Type', 'Downtime Events', 'Downtime (hrs)', 'Repair Cost'],
            'rows'    => array_map(fn($r) => [
                $r['Machine'], $r['Type'] ?? '-', (string)(int)$r['Events'],
                number_format((float)$r['DowntimeHours'], 2), number_format((float)$r['Cost'], 2),
            ], $rows),
        ];
    }

    private function power(string $from, string $to): array
    {
        $bySource = $this->query(
            "SELECT Source, COUNT(*) AS Entries, COALESCE(SUM(ConsumptionKWh),0) AS KWh,
                    COALESCE(SUM(Cost),0) AS Cost
             FROM power_usage WHERE Date BETWEEN ? AND ?
             GROUP BY Source",
            [$from, $to]
        );
        $gen = $this->query(
            "SELECT Date, RuntimeHrs, FuelUsed, FuelUnit, Reason
             FROM generator_log WHERE Date BETWEEN ? AND ? ORDER BY Date DESC",
            [$from, $to]
        );
        $totalKwh = array_sum(array_map(fn($r) => (float)$r['KWh'], $bySource));
        $totalCost = array_sum(array_map(fn($r) => (float)$r['Cost'], $bySource));
        $fuel = array_sum(array_map(fn($g) => (float)$g['FuelUsed'], $gen));
        $hours = array_sum(array_map(fn($g) => (float)$g['RuntimeHrs'], $gen));
        $rows = array_merge(
            array_map(fn($p) => ['Electricity', ucfirst($p['Source']), (string)(int)$p['Entries'],
                number_format((float)$p['KWh'], 2) . ' kWh', number_format((float)$p['Cost'], 2)], $bySource),
            array_map(fn($g) => ['Generator Run', $this->fmt($g['Date']), '-',
                number_format((float)$g['RuntimeHrs'], 2) . ' hrs / ' . number_format((float)$g['FuelUsed'], 2) . ' ' . $g['FuelUnit'],
                $g['Reason'] ?? '-'], $gen)
        );
        return [
            'title' => self::TYPES['power'],
            'period' => "$from to $to",
            'summary' => [
                'Electricity Used' => number_format($totalKwh, 2) . ' kWh',
                'Electricity Cost' => number_format($totalCost, 2),
                'Generator Hours'  => number_format($hours, 2),
                'Fuel Used'        => number_format($fuel, 2) . ' L',
            ],
            'headers' => ['Category', 'Source / Date', 'Entries', 'Consumption', 'Cost / Reason'],
            'rows'    => $rows,
        ];
    }

    private function packaging(string $from, string $to): array
    {
        $usage = $this->query(
            "SELECT pm.Name, pm.Type, pm.Unit, pm.CurrentStock, pm.MinStock,
                    COUNT(b.BatchID) AS BatchesUsing,
                    COALESCE(SUM(b.Quantity),0) As QtyConsumed
             FROM packaging_materials pm
             LEFT JOIN production_batches b
                    ON b.PackagingMaterialID = pm.PackageID
                   AND b.ProductionDate BETWEEN ? AND ?
             GROUP BY pm.PackageID, pm.Name, pm.Type, pm.Unit, pm.CurrentStock, pm.MinStock
             ORDER BY QtyConsumed DESC",
            [$from, $to]
        );
        return [
            'title' => self::TYPES['packaging'],
            'period' => "$from to $to",
            'summary' => [
                'Packaging Items' => count($usage),
                'Low Stock Items' => count(array_filter($usage, fn($r) => (float)$r['CurrentStock'] <= (float)$r['MinStock'])),
            ],
            'headers' => ['Packaging Material', 'Type', 'Batches Using', 'Qty Consumed', 'Current Stock', 'Min Stock'],
            'rows'    => array_map(fn($r) => [
                $r['Name'], $r['Type'] ?? '-', (string)(int)$r['BatchesUsing'],
                number_format((float)$r['QtyConsumed'], 2) . ' ' . $r['Unit'],
                number_format((float)$r['CurrentStock'], 2) . ' ' . $r['Unit'],
                number_format((float)$r['MinStock'], 2),
            ], $usage),
        ];
    }

    private function certification(): array
    {
        $rows = $this->query(
            "SELECT CertID, CertName, CertType, IssuingAuthority, IssueDate, ExpiryDate, Status
             FROM certifications ORDER BY ExpiryDate ASC"
        );
        $active = count(array_filter($rows, fn($r) => $r['Status'] === 'Active'));
        $expiring = count(array_filter($rows, fn($r) => $r['Status'] !== 'Expired'
            && strtotime((string)$r['ExpiryDate']) <= strtotime('+90 days')));
        return [
            'title' => self::TYPES['certification'],
            'period' => 'Compliance status as of ' . date('d M Y'),
            'summary' => [
                'Certificates'        => count($rows),
                'Active'              => $active,
                'Expiring in 90 Days' => $expiring,
            ],
            'headers' => ['ID', 'Certificate', 'Type', 'Authority', 'Issued', 'Expires', 'Days Left', 'Status'],
            'rows'    => array_map(function ($r) {
                $days = (int)ceil((strtotime((string)$r['ExpiryDate']) - time()) / 86400);
                return [$r['CertID'], $r['CertName'], $r['CertType'],
                        $r['IssuingAuthority'] ?? '-', $this->fmt($r['IssueDate']),
                        $this->fmt($r['ExpiryDate']),
                        $days < 0 ? abs($days) . ' overdue' : $days . 'd',
                        $r['Status']];
            }, $rows),
        ];
    }

    private function qaqc(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT q.InspectionID, q.InspectionDate, q.InspectionType, b.BatchNumber,
                    q.Result, q.DefectsFound, q.TestResults, q.Status, u.Name AS Inspector
             FROM quality_inspections q
             LEFT JOIN production_batches b ON q.BatchID = b.BatchID
             LEFT JOIN users u ON q.InspectorID = u.UserID
             WHERE q.InspectionDate BETWEEN ? AND ?
             ORDER BY q.InspectionDate DESC",
            [$from, $to]
        );
        $pass = count(array_filter($rows, fn($r) => $r['Result'] === 'Pass'));
        $fail = count(array_filter($rows, fn($r) => $r['Result'] === 'Fail'));
        $rate = ($pass + $fail) > 0 ? round($pass / ($pass + $fail) * 100, 1) : null;
        return [
            'title' => self::TYPES['qaqc'],
            'period' => "$from to $to",
            'summary' => [
                'Inspections'  => count($rows),
                'Pass / Fail'  => "$pass / $fail",
                'Pass Rate'    => $rate === null ? '-' : $rate . '%',
            ],
            'headers' => ['ID', 'Date', 'Stage', 'Batch', 'Inspector', 'Result', 'Defects Found', 'Status'],
            'rows'    => array_map(fn($r) => [
                $r['InspectionID'], $this->fmt($r['InspectionDate']), $r['InspectionType'],
                $r['BatchNumber'] ?? '-', $r['Inspector'] ?? '-', $r['Result'],
                $r['DefectsFound'] ?? '-', $r['Status'],
            ], $rows),
        ];
    }

    private function sop(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT t.Title, t.Department, c.Date, c.CompletedItems, c.TotalItems,
                    c.ApprovalStatus, u.Name AS Supervisor
             FROM sop_checklists c
             JOIN sop_templates t ON c.SOP_ID = t.SOP_ID
             LEFT JOIN users u ON c.SupervisorID = u.UserID
             WHERE c.Date BETWEEN ? AND ?
             ORDER BY c.Date DESC",
            [$from, $to]
        );
        $approved = count(array_filter($rows, fn($r) => $r['ApprovalStatus'] === 'Approved'));
        $itemsDone = array_sum(array_map(fn($r) => (int)$r['CompletedItems'], $rows));
        $itemsTotal = array_sum(array_map(fn($r) => (int)$r['TotalItems'], $rows));
        return [
            'title' => self::TYPES['sop'],
            'period' => "$from to $to",
            'summary' => [
                'Checklists Executed' => count($rows),
                'Supervisor Approved' => $approved,
                'Item Compliance'     => $itemsTotal > 0 ? round($itemsDone / $itemsTotal * 100, 1) . '%' : '-',
            ],
            'headers' => ['SOP', 'Department', 'Date', 'Completed', 'Total Items', 'Supervisor', 'Approval'],
            'rows'    => array_map(fn($r) => [
                $r['Title'], $r['Department'] ?? '-', $this->fmt($r['Date']),
                (string)(int)$r['CompletedItems'], (string)(int)$r['TotalItems'],
                $r['Supervisor'] ?? '-', $r['ApprovalStatus'],
            ], $rows),
        ];
    }

    private function profitLoss(string $from, string $to): array
    {
        $revenue = (float)$this->queryScalar(
            "SELECT COALESCE(SUM(TotalAmount),0) FROM sales_orders
             WHERE Status='Completed' AND OrderDate BETWEEN ? AND ?",
            [$from, $to]
        );
        $collected = (float)$this->queryScalar(
            "SELECT COALESCE(SUM(i.TotalDue),0) FROM invoices i
             JOIN sales_orders s ON i.OrderID = s.OrderID
             WHERE s.OrderDate BETWEEN ? AND ? AND i.PaymentStatus='Paid'",
            [$from, $to]
        );
        $maintCost = (float)$this->queryScalar(
            "SELECT COALESCE(SUM(Cost),0) FROM maintenance_records
             WHERE Status != 'Cancelled' AND MaintenanceDate BETWEEN ? AND ?",
            [$from, $to]
        );
        $powerCost = (float)$this->queryScalar(
            "SELECT COALESCE(SUM(Cost),0) FROM power_usage WHERE Date BETWEEN ? AND ?",
            [$from, $to]
        );
        $expenses = $maintCost + $powerCost;
        $net = $revenue - $expenses;
        return [
            'title' => self::TYPES['profit-loss'],
            'period' => "$from to $to",
            'summary' => [
                'Revenue (Completed Orders)' => number_format($revenue, 2),
                'Payments Collected'         => number_format($collected, 2),
                'Maintenance Cost'           => number_format($maintCost, 2),
                'Power Cost'                 => number_format($powerCost, 2),
                'Net Profit / Loss'          => number_format($net, 2),
            ],
            'headers' => ['Line Item', 'Amount'],
            'rows'    => [
                ['Revenue (completed orders)', number_format($revenue, 2)],
                ['Maintenance & repairs', '(' . number_format($maintCost, 2) . ')'],
                ['Electricity', '(' . number_format($powerCost, 2) . ')'],
                ['Net Profit / Loss', number_format($net, 2)],
            ],
        ];
    }

    private function oee(string $from, string $to): array
    {
        $rows = $this->query(
            "SELECT p.Date, m.Name AS Machine, p.Shift, p.PlannedRunTime, p.ActualRunTime,
                    p.TotalProduced, p.GoodProduced, p.DefectCount,
                    p.AvailabilityRate, p.PerformanceRate, p.QualityRate, p.OEE
             FROM production_efficiency p
             LEFT JOIN machines m ON p.MachineID = m.MachineID
             WHERE p.Date BETWEEN ? AND ?
             ORDER BY p.Date DESC",
            [$from, $to]
        );
        $avgOee = count($rows) > 0
            ? round(array_sum(array_map(fn($r) => (float)$r['OEE'], $rows)) / count($rows), 1)
            : 0;
        return [
            'title' => self::TYPES['oee'],
            'period' => "$from to $to",
            'summary' => [
                'Entries'      => count($rows),
                'Average OEE'  => $avgOee . '%',
            ],
            'headers' => ['Date', 'Machine', 'Shift', 'Planned (min)', 'Actual (min)', 'Produced', 'Good', 'Defects', 'A %', 'P %', 'Q %', 'OEE %'],
            'rows'    => array_map(fn($r) => [
                $this->fmt($r['Date']), $r['Machine'] ?? '-', $r['Shift'] ?? '-',
                number_format((float)$r['PlannedRunTime'], 0), number_format((float)$r['ActualRunTime'], 0),
                (string)(int)$r['TotalProduced'], (string)(int)$r['GoodProduced'], (string)(int)$r['DefectCount'],
                number_format((float)$r['AvailabilityRate'], 1), number_format((float)$r['PerformanceRate'], 1),
                number_format((float)$r['QualityRate'], 1), number_format((float)$r['OEE'], 1),
            ], $rows),
        ];
    }
}
