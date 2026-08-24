<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';

/**
 * System Settings -> Backup & Restore.
 * Admin-only (module 'backup' is granted to the factory manager role alone).
 * Backup streams a full SQL dump of every table; Restore re-runs a dump
 * produced by this same tool inside a transaction.
 */
class SettingsController extends Controller {
    private const MAX_RESTORE_BYTES = 52428800; // 50 MB

    public function __construct() {
        parent::__construct();
        $this->viewPath = 'settings';
    }

    public function index(): void {
        if (!canEdit('backup')) {
            setFlash('error', 'Access denied. Insufficient permissions.');
            $this->redirect('dashboard');
            return;
        }
        // Same route serves the page (GET) and streams the dump (POST).
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->streamBackup();
            return;
        }
        $db = getDb();
        $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
        $stats = [];
        foreach ($tables as $table) {
            $rows = (int) $db->query("SELECT COUNT(*) FROM `" . str_replace('`', '', $table) . "`")->fetchColumn();
            $stats[$table] = $rows;
        }
        $this->render('backup', ['stats' => $stats]);
    }

    public function streamBackup(): void {
        if (!canEdit('backup')) {
            setFlash('error', 'Access denied. Insufficient permissions.');
            $this->redirect('dashboard');
            return;
        }
        $db = getDb();
        $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);

        $filename = 'freshjuice_backup_' . date('Y-m-d_His') . '.sql';
        if (!headers_sent()) {
            header('Content-Type: application/sql; charset=UTF-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-store');
        }

        echo "-- Fresh Fruit Juice Production Factory Management System\n";
        echo '-- Backup generated ' . date('Y-m-d H:i:s') . " by {$_SESSION['user_id']}\n";
        echo "-- Restore with the built-in Restore tool or: mysql -u user -p dbname < thisfile.sql\n\n";
        echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $safe = str_replace('`', '', (string)$table);
            $create = $db->query("SHOW CREATE TABLE `$safe`")->fetch();
            echo "-- -------------------------------\n";
            echo "-- Table: $safe\n";
            echo "-- -------------------------------\n";
            echo "DROP TABLE IF EXISTS `$safe`;\n";
            echo ($create['Create Table'] ?? '') . ";\n\n";

            $stmt = $db->query("SELECT * FROM `$safe`");
            $batch = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $vals = array_map(fn($v) => $v === null ? 'NULL' : $db->quote((string)$v), array_values($row));
                $batch[] = '(' . implode(', ', $vals) . ')';
                if (count($batch) >= 100) {
                    echo 'INSERT INTO `' . $safe . '` VALUES ' . implode(",\n", $batch) . ";\n";
                    $batch = [];
                }
            }
            if ($batch) {
                echo 'INSERT INTO `' . $safe . '` VALUES ' . implode(",\n", $batch) . ";\n";
            }
            echo "\n";
        }
        echo "SET FOREIGN_KEY_CHECKS=1;\n";

        logAudit($_SESSION['user_id'] ?? null, 'BACKUP', 'System', null, 'Downloaded database backup');
        exit;
    }

    public function restore(): void {
        if (!canEdit('backup')) {
            setFlash('error', 'Access denied. Insufficient permissions.');
            $this->redirect('dashboard');
            return;
        }
        $file = $_FILES['backup_file'] ?? null;
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            setFlash('error', 'Please choose a backup file to restore.');
            $this->redirect('settings/backup');
            return;
        }
        if ((int)$file['size'] > self::MAX_RESTORE_BYTES) {
            setFlash('error', 'Backup file is too large (max 50 MB).');
            $this->redirect('settings/backup');
            return;
        }
        $ext = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['sql', 'txt'], true)) {
            setFlash('error', 'Invalid file type. Upload a .sql backup file.');
            $this->redirect('settings/backup');
            return;
        }
        if (($this->getInput('confirm') ?? '') !== 'RESTORE') {
            setFlash('error', 'Type RESTORE in the confirmation box to proceed.');
            $this->redirect('settings/backup');
            return;
        }

        $sql = (string)file_get_contents((string)$file['tmp_name']);
        if (stripos($sql, 'Fresh Fruit Juice Production Factory Management System') === false
            && stripos($sql, 'CREATE TABLE') === false) {
            setFlash('error', 'This does not look like a valid SQL backup file.');
            $this->redirect('settings/backup');
            return;
        }

        $db = getDb();
        $statements = $this->splitSql($sql);
        $db->beginTransaction();
        try {
            $db->exec('SET FOREIGN_KEY_CHECKS=0');
            foreach ($statements as $statement) {
                if ($statement === '') continue;
                $db->exec($statement);
            }
            $db->exec('SET FOREIGN_KEY_CHECKS=1');
            $db->commit();
            logAudit($_SESSION['user_id'] ?? null, 'RESTORE', 'System', null, 'Restored database from backup (' . count($statements) . ' statements)');
            setFlash('success', 'Database restored successfully from ' . sanitize(basename((string)$file['name'])) . '.');
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('Restore failed: ' . $e->getMessage());
            setFlash('error', 'Restore failed and was rolled back. The database is unchanged. Details are in the error log.');
        }
        $this->redirect('settings/backup');
    }

    /** Split a dumped .sql file into individual statements on ";" line ends. */
    private function splitSql(string $sql): array {
        $lines = preg_split('/\r?\n/', $sql) ?: [];
        $statements = [];
        $buffer = '';
        foreach ($lines as $line) {
            $trimmed = ltrim($line);
            if ($trimmed === '' || $trimmed[0] === '-' || str_starts_with($trimmed, '#')) continue;
            $buffer .= $line . "\n";
            if (preg_match('/;\s*$/', $line)) {
                $statements[] = trim($buffer);
                $buffer = '';
            }
        }
        if (trim($buffer) !== '') $statements[] = trim($buffer);
        return $statements;
    }
}
