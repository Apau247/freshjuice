<?php
declare(strict_types=1);
// Throwaway CLI harness: verifies every new report/traceability/alerts query runs.
require __DIR__ . '/../backend/config/database.php';
require_once __DIR__ . '/../backend/models/Model.php';
spl_autoload_register(function (string $class): void {
    $file = dirname(__DIR__) . '/backend/models/' . $class . '.php';
    if (file_exists($file)) require_once $file;
});

$fail = 0;
try {
    $db = getDb();
    echo "Connected to " . DB_NAME . "\n";
} catch (Throwable $e) {
    fwrite(STDERR, "DB unavailable: " . $e->getMessage() . "\n");
    exit(2);
}

// can() stub: full access for the harness
function can(string $m): bool { return true; }

$from = date('Y-m-d', strtotime('-1 year'));
$to   = date('Y-m-d');

foreach (ReportModel::TYPES as $type => $label) {
    try {
        $data = (new ReportModel())->build($type, $from, $to);
        echo sprintf("OK  %-14s rows=%d headers=%d\n", $type, count($data['rows']), count($data['headers']));
    } catch (Throwable $e) {
        $fail++;
        echo sprintf("FAIL %-14s %s\n", $type, $e->getMessage());
    }
}

// Traceability query (copied from QualityController::traceability)
$sql = "SELECT b.BatchID,
            d.DeliveryDate AS DeliveryDate,
            (SELECT GROUP_CONCAT(DISTINCT q.Result ORDER BY q.Result SEPARATOR '/')
               FROM quality_inspections q WHERE q.BatchID = b.BatchID) AS QCResults,
            (SELECT COUNT(*) FROM sales_orders so WHERE so.FG_ID = fg.FG_ID) AS OrderCount
     FROM production_batches b
     LEFT JOIN raw_materials rm ON b.RawMaterialID = rm.MaterialID
     LEFT JOIN suppliers s ON rm.SupplierID = s.SupplierID
     LEFT JOIN supplier_deliveries d ON d.SupplierID = s.SupplierID
           AND d.ItemName LIKE CONCAT('%%', LEFT(rm.Name, 10), '%%')
     LEFT JOIN packaging_materials pm ON b.PackagingMaterialID = pm.PackageID
     LEFT JOIN machines m ON b.MachineID = m.MachineID
     LEFT JOIN users u ON b.UserID = u.UserID
     LEFT JOIN finished_goods fg ON fg.BatchID = b.BatchID
     ORDER BY b.ProductionDate DESC LIMIT 300";
try {
    $stmt = $db->prepare($sql);
    $stmt->execute();
    echo "OK  traceability rows=" . count($stmt->fetchAll()) . "\n";
} catch (Throwable $e) {
    $fail++;
    echo "FAIL traceability: " . $e->getMessage() . "\n";
}

// Alerts queries from InventoryController
$alertsSqls = [
    'low_stock_raw' => "SELECT rm.MaterialID FROM raw_materials rm LEFT JOIN suppliers s ON rm.SupplierID = s.SupplierID WHERE rm.CurrentStock <= rm.MinStock AND rm.Status='Active'",
    'low_stock_pkg' => "SELECT pm.PackageID FROM packaging_materials pm WHERE pm.CurrentStock <= pm.MinStock AND pm.Status='Active'",
    'expiry_fg'     => "SELECT fg.FG_ID FROM finished_goods fg LEFT JOIN production_batches b ON fg.BatchID=b.BatchID WHERE fg.ExpiryDate > CURDATE() AND fg.ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)",
    'expired_fg'    => "SELECT fg.FG_ID FROM finished_goods fg WHERE fg.ExpiryDate <= CURDATE()",
    'maint_due'     => "SELECT r.MaintenanceID FROM maintenance_records r LEFT JOIN machines m ON r.MachineID=m.MachineID WHERE r.Status='Scheduled' AND r.MaintenanceDate <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)",
];
foreach ($alertsSqls as $name => $sql) {
    try {
        $db->query($sql)->fetchAll();
        echo "OK  $name\n";
    } catch (Throwable $e) {
        $fail++;
        echo "FAIL $name: " . $e->getMessage() . "\n";
    }
}

// Settings backup table enumeration
try {
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "OK  show-tables (" . count($tables) . " tables)\n";
} catch (Throwable $e) {
    $fail++;
    echo "FAIL show-tables: " . $e->getMessage() . "\n";
}

exit($fail > 0 ? 1 : 0);
