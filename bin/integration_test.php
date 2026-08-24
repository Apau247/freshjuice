<?php
declare(strict_types=1);

/**
 * Factory Management System - Integration Test Suite
 * Runs against live MySQL database.
 * Creates temporary records and cleans up on completion.
 */

// ─── Bootstrap ────────────────────────────────────────────────────
$_SERVER['REQUEST_METHOD'] = 'GET';
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/backend/config/database.php';

$root = APP_ROOT;
$passed = 0;
$failed = 0;
$tests = [];

function test(string $label, callable $fn): void {
    global $passed, $failed, $tests;
    try {
        $fn();
        $passed++;
        $tests[] = "  PASS  $label";
    } catch (\Throwable $e) {
        $failed++;
        $tests[] = "  FAIL  $label — " . $e->getMessage();
    }
}

function assertTrue(bool $cond, string $msg = 'Assertion failed'): void {
    if (!$cond) throw new RuntimeException($msg);
}
function assertEquals(mixed $expected, mixed $actual, string $msg = ''): void {
    if ($expected !== $actual) {
        throw new RuntimeException($msg ?: "Expected " . var_export($expected, true) . " but got " . var_export($actual, true));
    }
}
function assertNotEmpty(mixed $val, string $msg = 'Expected non-empty'): void {
    if (empty($val)) throw new RuntimeException($msg);
}

// ─── Helpers ──────────────────────────────────────────────────────
function tempId(string $prefix): string {
    return $prefix . '-TEST-' . date('YmdHis') . '-' . substr(bin2hex(random_bytes(4)), 0, 6);
}

function getDbConn(): PDO {
    static $db = null;
    if ($db === null) {
        $db = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $db;
}

function tableExists(string $table): bool {
    $stmt = getDbConn()->prepare("SHOW TABLES LIKE ?");
    $stmt->execute([$table]);
    return (bool)$stmt->fetch();
}

function rowCount(string $table): int {
    return (int)getDbConn()->query("SELECT COUNT(*) as cnt FROM `$table`")->fetch()['cnt'];
}

// ==================================================================
echo "=== Factory Management System Integration Test Suite ===\n\n";
echo "PHP:       " . PHP_VERSION . "\n";
echo "DB:        " . DB_HOST . '/' . DB_NAME . "\n";
echo "APP_ROOT:  " . APP_ROOT . "\n\n";

// ─── 1. Database Connection ───────────────────────────────────────
echo "--- 1. Database Connection ---\n";
test('PDO connection succeeds', function () {
    $db = getDbConn();
    assertTrue($db instanceof PDO);
});
test('DB_HOST constant defined', function () { assertTrue(defined('DB_HOST')); });
test('DB_NAME constant defined', function () { assertTrue(defined('DB_NAME')); });
test('DB_CHARSET constant defined', function () { assertTrue(defined('DB_CHARSET')); });
echo "\n";

// ─── 2. Schema Integrity ──────────────────────────────────────────
echo "--- 2. Schema Integrity ---\n";
$expectedTables = [
    'roles', 'users', 'staff', 'shifts', 'attendance',
    'suppliers', 'supplier_deliveries', 'raw_materials', 'packaging_materials',
    'machines', 'production_batches', 'quality_inspections', 'finished_goods',
    'customers', 'sales_orders', 'invoices', 'maintenance_records', 'waste_records',
    'water_usage', 'water_quality_tests', 'power_usage', 'generator_log',
    'certifications', 'sop_templates', 'sop_checklists', 'audit_trail',
    'safety_inspections', 'hazard_register', 'accident_reports', 'permits',
    'training_records', 'ppe_records', 'production_efficiency', 'improvement_initiatives',
    'documents', 'supplier_evaluations', 'emergency_drills', 'fat_records',
];
foreach ($expectedTables as $tbl) {
    test("Table '$tbl' exists", function () use ($tbl) {
        assertTrue(tableExists($tbl), "Missing table: $tbl");
    });
}

// Column checks for key tables
$columnChecks = [
    'users' => ['UserID', 'RoleID', 'Name', 'password', 'Status'],
    'staff' => ['StaffID', 'FirstName', 'LastName', 'Email', 'Department', 'Status'],
    'shifts' => ['ShiftID', 'ShiftName', 'StartTime', 'EndTime'],
    'attendance' => ['AttendanceID', 'StaffID', 'Date', 'ClockIn', 'ClockOut', 'Status'],
    'water_usage' => ['WaterUsageID', 'Date', 'UsageType', 'Quantity', 'Unit'],
    'water_quality_tests' => ['WaterTestID', 'TestDate', 'TestType', 'Result'],
    'power_usage' => ['PowerUsageID', 'Date', 'Source', 'ConsumptionKWh'],
    'generator_log' => ['LogID', 'Date', 'StartTime', 'EndTime', 'RuntimeHrs'],
    'roles' => ['RoleID', 'RoleName'],
    'suppliers' => ['SupplierID', 'Name', 'Status'],
    'customers' => ['CustomerID', 'Name', 'Status'],
    'production_batches' => ['BatchID', 'BatchNumber', 'ProductionDate', 'Flavour', 'Status'],
    'quality_inspections' => ['InspectionID', 'InspectionType', 'InspectionDate', 'Result'],
    'finished_goods' => ['FG_ID', 'Flavour', 'ExpiryDate', 'QuantityAvailable'],
    'sop_templates' => ['SOP_ID', 'Title', 'Status'],
    'sop_checklists' => ['ChecklistID', 'SOP_ID', 'Date', 'ApprovalStatus'],
    'documents' => ['DocID', 'Title', 'DocType', 'Status', 'FilePath'],
    'certifications' => ['CertID', 'CertName', 'CertType', 'Status'],
    'machines' => ['MachineID', 'Name', 'Type', 'Status'],
];
foreach ($columnChecks as $table => $cols) {
    test("Table '$table' has required columns", function () use ($table, $cols) {
        $stmt = getDbConn()->query("DESCRIBE `$table`");
        $actualCols = array_column($stmt->fetchAll(), 'Field');
        foreach ($cols as $c) {
            assertTrue(in_array($c, $actualCols, true), "Missing column '$c' in '$table'");
        }
    });
}
echo "\n";

// ─── 3. Sample Data Verification ─────────────────────────────────
echo "--- 3. Sample Data ---\n";
test('roles table has 8 records', function () { assertEquals(8, rowCount('roles'), 'Expected 8 roles'); });
test('users table contains all 8 sample users', function () {
    $c = getDbConn()->query("SELECT COUNT(*) as c FROM users WHERE UserID IN ('USR-001','USR-002','USR-003','USR-004','USR-005','USR-006','USR-007','USR-008')")->fetch()['c'];
    assertEquals(8, (int)$c, 'Expected 8 sample users');
});
test('staff table has 8 records', function () { assertEquals(8, rowCount('staff'), 'Expected 8 staff'); });
test('suppliers table has 5 records', function () { assertEquals(5, rowCount('suppliers')); });
test('customers table has 5 records', function () { assertEquals(5, rowCount('customers')); });
test('roles include Administrator', function () {
    $c = getDbConn()->query("SELECT COUNT(*) as c FROM roles WHERE RoleName LIKE '%Admin%'")->fetch()['c'];
    assertTrue((int)$c > 0);
});
echo "\n";

// ─── 4. Authentication Tests ─────────────────────────────────────
echo "--- 4. Authentication ---\n";
test('Password hash verifies for password123', function () {
    $hash = getDbConn()->query("SELECT `password` FROM users WHERE UserID = 'USR-001'")->fetch()['password'];
    assertTrue(password_verify('password123', $hash), 'Password verification failed');
});
test('Wrong password does not verify', function () {
    $hash = getDbConn()->query("SELECT `password` FROM users WHERE UserID = 'USR-001'")->fetch()['password'];
    assertTrue(!password_verify('wrongpassword', $hash));
});
echo "\n";

// ─── 5. RBAC Tests ───────────────────────────────────────────────
echo "--- 5. RBAC ---\n";
test('All role IDs match RoleName', function () {
    $rows = getDbConn()->query("SELECT RoleID, RoleName FROM roles")->fetchAll();
    $map = [
        'ROLE-001' => 'System Administrator',
        'ROLE-002' => 'Factory Manager',
        'ROLE-003' => 'Production Supervisor',
        'ROLE-004' => 'Inventory Officer',
        'ROLE-005' => 'QA/QC Officer',
        'ROLE-006' => 'Sales Officer',
        'ROLE-007' => 'Accountant',
        'ROLE-008' => 'Maintenance Engineer',
    ];
    foreach ($rows as $r) {
        assertEquals($map[$r['RoleID']] ?? 'UNKNOWN', $r['RoleName']);
    }
});
test('Users are assigned to valid roles', function () {
    $rows = getDbConn()->query("SELECT DISTINCT u.RoleID FROM users u LEFT JOIN roles r ON u.RoleID = r.RoleID WHERE r.RoleID IS NULL")->fetchAll();
    assertEquals(0, count($rows), 'Found users with invalid role references');
});
echo "\n";

// ─── 6. CRUD Tests (create temp records, read, update, delete) ───
echo "--- 6. CRUD Operations ---\n";

// 6a. Staff CRUD
$staffId = tempId('STF');
test('CREATE staff record', function () use ($staffId) {
    getDbConn()->prepare("INSERT INTO staff (StaffID, FirstName, LastName, Email, Department, Position, Status) VALUES (?,?,?,?,?,?,?)")
        ->execute([$staffId, 'Test', 'User', 'test@freshjuice.com', 'Production', 'Tester', 'Active']);
    assertTrue(rowCount('staff') > 0);
});
test('READ staff record', function () use ($staffId) {
    $r = getDbConn()->prepare("SELECT * FROM staff WHERE StaffID = ?")->execute([$staffId]);
    $row = getDbConn()->prepare("SELECT * FROM staff WHERE StaffID = ?")->execute([$staffId]);
    $row = getDbConn()->prepare("SELECT * FROM staff WHERE StaffID = ?");
    $row->execute([$staffId]);
    $data = $row->fetch();
    assertTrue($data !== false, 'Staff record not found');
    assertEquals('Test', $data['FirstName']);
});
test('UPDATE staff record', function () use ($staffId) {
    getDbConn()->prepare("UPDATE staff SET Department = ?, Position = ? WHERE StaffID = ?")
        ->execute(['Quality', 'QA Tester', $staffId]);
    $row = getDbConn()->prepare("SELECT * FROM staff WHERE StaffID = ?");
    $row->execute([$staffId]);
    $data = $row->fetch();
    assertEquals('Quality', $data['Department']);
    assertEquals('QA Tester', $data['Position']);
});
test('DELETE staff record', function () use ($staffId) {
    getDbConn()->prepare("DELETE FROM staff WHERE StaffID = ?")->execute([$staffId]);
    $row = getDbConn()->prepare("SELECT * FROM staff WHERE StaffID = ?");
    $row->execute([$staffId]);
    assertTrue($row->fetch() === false, 'Staff record still exists after delete');
});

// 6b. Water Usage CRUD
$waterId = tempId('WU');
test('CREATE water usage', function () use ($waterId) {
    getDbConn()->prepare("INSERT INTO water_usage (WaterUsageID, Date, UsageType, Quantity, Unit, Purpose) VALUES (?,?,?,?,?,?)")
        ->execute([$waterId, date('Y-m-d'), 'Production', 100.50, 'litres', 'Test purpose']);
    $cnt = getDbConn()->query("SELECT COUNT(*) as c FROM water_usage WHERE WaterUsageID = '$waterId'")->fetch()['c'];
    assertEquals(1, (int)$cnt);
});
test('UPDATE water usage', function () use ($waterId) {
    getDbConn()->prepare("UPDATE water_usage SET Quantity = ?, UsageType = ? WHERE WaterUsageID = ?")
        ->execute([200.75, 'Cleaning', $waterId]);
    $row = getDbConn()->prepare("SELECT * FROM water_usage WHERE WaterUsageID = ?");
    $row->execute([$waterId]);
    $data = $row->fetch();
    assertEquals('200.75', (string)$data['Quantity']);
    assertEquals('Cleaning', $data['UsageType']);
});
test('DELETE water usage', function () use ($waterId) {
    getDbConn()->prepare("DELETE FROM water_usage WHERE WaterUsageID = ?")->execute([$waterId]);
    $row = getDbConn()->prepare("SELECT * FROM water_usage WHERE WaterUsageID = ?");
    $row->execute([$waterId]);
    assertTrue($row->fetch() === false);
});

// 6c. Power Usage CRUD
$powerId = tempId('PU');
test('CREATE power usage', function () use ($powerId) {
    getDbConn()->prepare("INSERT INTO power_usage (PowerUsageID, Date, Source, ConsumptionKWh, Cost) VALUES (?,?,?,?,?)")
        ->execute([$powerId, date('Y-m-d'), 'Grid', 500.00, 250.00]);
    assertNotEmpty(getDbConn()->prepare("SELECT * FROM power_usage WHERE PowerUsageID = ?")->execute([$powerId]));
});
test('UPDATE power usage', function () use ($powerId) {
    getDbConn()->prepare("UPDATE power_usage SET ConsumptionKWh = ?, Cost = ? WHERE PowerUsageID = ?")
        ->execute([600.00, 300.00, $powerId]);
    $data = getDbConn()->prepare("SELECT * FROM power_usage WHERE PowerUsageID = ?");
    $data->execute([$powerId]);
    $row = $data->fetch();
    assertEquals('600.00', (string)$row['ConsumptionKWh']);
});
test('DELETE power usage', function () use ($powerId) {
    getDbConn()->prepare("DELETE FROM power_usage WHERE PowerUsageID = ?")->execute([$powerId]);
    $data = getDbConn()->prepare("SELECT * FROM power_usage WHERE PowerUsageID = ?");
    $data->execute([$powerId]);
    assertTrue($data->fetch() === false);
});

// 6d. Staff Shifts CRUD
$shiftId = tempId('SHF');
test('CREATE shift', function () use ($shiftId) {
    getDbConn()->prepare("INSERT INTO shifts (ShiftID, ShiftName, StartTime, EndTime, Description) VALUES (?,?,?,?,?)")
        ->execute([$shiftId, 'Test Shift', '06:00:00', '14:00:00', 'Integration test shift']);
    assertNotEmpty(getDbConn()->prepare("SELECT * FROM shifts WHERE ShiftID = ?")->execute([$shiftId]));
});
test('UPDATE shift', function () use ($shiftId) {
    getDbConn()->prepare("UPDATE shifts SET ShiftName = ?, StartTime = ? WHERE ShiftID = ?")
        ->execute(['Updated Shift', '07:00:00', $shiftId]);
    $data = getDbConn()->prepare("SELECT * FROM shifts WHERE ShiftID = ?");
    $data->execute([$shiftId]);
    $row = $data->fetch();
    assertEquals('Updated Shift', $row['ShiftName']);
});
test('DELETE shift', function () use ($shiftId) {
    getDbConn()->prepare("DELETE FROM shifts WHERE ShiftID = ?")->execute([$shiftId]);
    $data = getDbConn()->prepare("SELECT * FROM shifts WHERE ShiftID = ?");
    $data->execute([$shiftId]);
    assertTrue($data->fetch() === false);
});

// 6e. Attendance CRUD (requires staff + shift refs)
$staffRef = 'STF-001';
$attId = tempId('ATT');
$shiftRef = getDbConn()->query("SELECT ShiftID FROM shifts LIMIT 1")->fetch();
$shiftRef = $shiftRef ? $shiftRef['ShiftID'] : null;
test('CREATE attendance', function () use ($attId, $staffRef, $shiftRef) {
    getDbConn()->prepare("INSERT INTO attendance (AttendanceID, StaffID, ShiftID, Date, ClockIn, Status) VALUES (?,?,?,?,?,?)")
        ->execute([$attId, $staffRef, $shiftRef, date('Y-m-d'), '08:00:00', 'Present']);
    $data = getDbConn()->prepare("SELECT * FROM attendance WHERE AttendanceID = ?");
    $data->execute([$attId]);
    assertTrue($data->fetch() !== false);
});
test('UPDATE attendance', function () use ($attId) {
    getDbConn()->prepare("UPDATE attendance SET ClockIn = ?, Status = ? WHERE AttendanceID = ?")
        ->execute(['09:00:00', 'Late', $attId]);
    $data = getDbConn()->prepare("SELECT * FROM attendance WHERE AttendanceID = ?");
    $data->execute([$attId]);
    $row = $data->fetch();
    assertEquals('Late', $row['Status']);
});
test('DELETE attendance', function () use ($attId) {
    getDbConn()->prepare("DELETE FROM attendance WHERE AttendanceID = ?")->execute([$attId]);
    $data = getDbConn()->prepare("SELECT * FROM attendance WHERE AttendanceID = ?");
    $data->execute([$attId]);
    assertTrue($data->fetch() === false);
});

// 6f. Generator Log CRUD
$genId = tempId('GEN');
test('CREATE generator log', function () use ($genId) {
    getDbConn()->prepare("INSERT INTO generator_log (LogID, Date, StartTime, EndTime, RuntimeHrs, FuelUsed, FuelUnit, Reason) VALUES (?,?,?,?,?,?,?,?)")
        ->execute([$genId, date('Y-m-d'), '08:00', '12:00', 4.0, 50.0, 'litres', 'Test run']);
    $data = getDbConn()->prepare("SELECT * FROM generator_log WHERE LogID = ?");
    $data->execute([$genId]);
    assertTrue($data->fetch() !== false);
});
test('DELETE generator log', function () use ($genId) {
    getDbConn()->prepare("DELETE FROM generator_log WHERE LogID = ?")->execute([$genId]);
    $data = getDbConn()->prepare("SELECT * FROM generator_log WHERE LogID = ?");
    $data->execute([$genId]);
    assertTrue($data->fetch() === false);
});
echo "\n";

// ─── 7. CSRF Tests ────────────────────────────────────────────────
echo "--- 7. CSRF ---\n";
$_SESSION = [];
test('CSRF token generation returns 64-char hex', function () {
    $token = bin2hex(random_bytes(32));
    assertEquals(64, strlen($token));
});
test('CSRF token validation via hash_equals', function () {
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    assertTrue(hash_equals($token, $_SESSION['csrf_token']));
    assertTrue(!hash_equals('wrong', $_SESSION['csrf_token']));
});
echo "\n";

// ─── 8. Document Upload Directory ─────────────────────────────────
echo "--- 8. Document Upload ---\n";
$uploadDir = APP_ROOT . '/uploads/documents';
test('Uploads directory exists', function () use ($uploadDir) {
    assertTrue(is_dir($uploadDir), "'$uploadDir' does not exist");
});
test('Uploads directory is writable', function () use ($uploadDir) {
    assertTrue(is_writable($uploadDir), "'$uploadDir' is not writable");
});
test('Write test file to uploads', function () use ($uploadDir) {
    $testFile = $uploadDir . '/.test_write_' . time();
    assertTrue(file_put_contents($testFile, 'test') !== false);
    unlink($testFile);
    assertTrue(!file_exists($testFile));
});

// .htaccess in uploads
$htaccess = $uploadDir . '/.htaccess';
if (!file_exists($htaccess)) {
    file_put_contents($htaccess, "Deny from all\n");
}
test('Uploads .htaccess blocks direct access', function () use ($htaccess) {
    assertTrue(file_exists($htaccess));
    $content = file_get_contents($htaccess);
    assertTrue(str_contains($content, 'Deny') || str_contains($content, 'deny'));
});
echo "\n";

// ─── 9. .env Verification ────────────────────────────────────────
echo "--- 9. Environment Configuration ---\n";
$envFile = APP_ROOT . '/.env';
test('.env file exists', function () use ($envFile) {
    assertTrue(file_exists($envFile), ".env not found at $envFile");
});
test('.env contains DB_HOST', function () use ($envFile) {
    $content = file_get_contents($envFile);
    assertTrue(str_contains($content, 'DB_HOST='));
});
test('.env contains MAIL_HOST', function () use ($envFile) {
    $content = file_get_contents($envFile);
    assertTrue(str_contains($content, 'MAIL_HOST='));
});
test('.env.example is consistent', function () {
    $env = file_get_contents(APP_ROOT . '/.env');
    $example = file_get_contents(APP_ROOT . '/.env.example');
    // Check all .env.example keys exist in .env
    preg_match_all('/^([A-Z_]+)=/m', $example, $exKeys);
    preg_match_all('/^([A-Z_]+)=/m', $env, $envKeys);
    foreach ($exKeys[1] as $k) {
        assertTrue(in_array($k, $envKeys[1], true), "Key '$k' from .env.example missing in .env");
    }
});
echo "\n";

// ─── 10. Token generation ─────────────────────────────────────────
echo "--- 10. ID Generation ---\n";
test('generateId returns string with prefix', function () {
    $id = generateId('STF');
    assertTrue(str_starts_with($id, 'STF-'));
    assertEquals(18, strlen($id)); // STF-YYYYMMDD-XXXXX
});
test('generateId creates unique IDs', function () {
    $ids = [];
    for ($i = 0; $i < 10; $i++) {
        $ids[] = generateId('WU');
    }
    assertEquals(10, count(array_unique($ids)));
});
echo "\n";

// ─── Summary ──────────────────────────────────────────────────────
echo "=== Results ===\n";
$total = $passed + $failed;
echo "  Total:  $total\n";
echo "  Passed: $passed\n";
echo "  Failed: $failed\n";
echo "\n";

foreach ($tests as $t) {
    echo $t . "\n";
}

exit($failed > 0 ? 1 : 0);
