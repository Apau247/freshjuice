<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';

/**
 * Hardware integration (barcode scanners): a scanner types a code into the
 * navbar "Scan" box and presses Enter; this controller resolves the code to
 * the module that owns it and lands the user on that list, pre-filtered (?q=).
 */
class ScanController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->viewPath = 'dashboard'; // never renders
    }

    public function lookup(): void {
        $code = strtoupper(trim($this->getInput('code')));
        if ($code === '') {
            $this->redirect('dashboard');
            return;
        }

        $db = getDb();
        $like = '%' . $code . '%';

        // Exact identifier matches first, then batch numbers.
        $checks = [
            ['SELECT BatchID FROM production_batches WHERE BatchID = ? OR BatchNumber = ?', [$code], 'production', '?route=production&q=' . urlencode($code)],
            ['SELECT MaterialID FROM raw_materials WHERE MaterialID = ?', [$code], 'materials', '?route=materials/raw&q=' . urlencode($code)],
            ['SELECT PackageID FROM packaging_materials WHERE PackageID = ?', [$code], 'materials', '?route=materials/packaging&q=' . urlencode($code)],
            ['SELECT FG_ID FROM finished_goods WHERE FG_ID = ?', [$code], 'finished_goods', '?route=finished-goods&q=' . urlencode($code)],
            ['SELECT CertID FROM certifications WHERE CertID = ?', [$code], 'certifications', '?route=certifications&q=' . urlencode($code)],
            ['SELECT MachineID FROM machines WHERE MachineID = ?', [$code], 'machines', '?route=machines&q=' . urlencode($code)],
            ['SELECT WasteID FROM waste_records WHERE WasteID = ?', [$code], 'waste', '?route=waste&q=' . urlencode($code)],
            // Partial matches as fallback (scanned partial labels / typed names).
            ['SELECT BatchID FROM production_batches WHERE BatchNumber LIKE ?', [$like], 'production', '?route=production&q=' . urlencode($code)],
            ['SELECT MaterialID FROM raw_materials WHERE Name LIKE ? OR MaterialID LIKE ?', [$like, $like], 'materials', '?route=materials/raw&q=' . urlencode($code)],
            ['SELECT PackageID FROM packaging_materials WHERE Name LIKE ? OR PackageID LIKE ?', [$like, $like], 'materials', '?route=materials/packaging&q=' . urlencode($code)],
            ['SELECT FG_ID FROM finished_goods WHERE FG_ID LIKE ?', [$like], 'finished_goods', '?route=finished-goods&q=' . urlencode($code)],
        ];

        foreach ($checks as [$sql, $params, $module, $target]) {
            if (!can($module)) continue;
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            if ($stmt->fetchColumn() !== false) {
                logAudit($_SESSION['user_id'] ?? null, 'SCAN', ucfirst($module), $code, 'Barcode scan resolved to ' . $module);
                header('Location: ' . $target);
                exit;
            }
        }

        setFlash('error', "No record found for code \"{$code}\".");
        $this->redirect('dashboard');
    }
}
