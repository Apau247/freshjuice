<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/ProductionBatchModel.php';
require_once __DIR__ . '/../models/RawMaterialModel.php';
require_once __DIR__ . '/../models/PackagingMaterialModel.php';
require_once __DIR__ . '/../models/MachineModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class ProductionController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new ProductionBatchModel();
        $this->viewPath = 'production';
    }

    public function index(): void {
        $this->render('index', ['batches' => $this->model->getAllDetailed()]);
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->getInput('BatchID') ?: generateId('BAT');
            $batchNum = $this->getInput('batch_number');
            $qty = (float)$this->getInput('quantity', '0');
            $rawMat = $this->getInput('raw_material_id');
            $pkgMat = $this->getInput('packaging_material_id');
            $status = $this->getInput('status', 'Pending');

            if (empty($batchNum)) {
                setFlash('error', 'Batch number is required.');
                $this->redirect('production');
                return;
            }
            if ($qty <= 0) {
                setFlash('error', 'Quantity must be greater than zero.');
                $this->redirect('production');
                return;
            }

            $db = getDb();
            $db->beginTransaction();
            try {
                if ($rawMat && $status !== 'Cancelled') {
                    $rmModel = new RawMaterialModel();
                    $rm = $rmModel->find($rawMat);
                    if ($rm && $rm['CurrentStock'] < $qty) {
                        $db->rollBack();
                        setFlash('error', 'Insufficient raw material stock. Available: ' . $rm['CurrentStock']);
                        $this->redirect('production');
                        return;
                    }
                    $rmModel->updateStock($rawMat, -$qty);
                }
                if ($pkgMat && $status !== 'Cancelled') {
                    $pkgModel = new PackagingMaterialModel();
                    $pkg = $pkgModel->find($pkgMat);
                    if ($pkg && $pkg['CurrentStock'] < $qty) {
                        $db->rollBack();
                        setFlash('error', 'Insufficient packaging material stock. Available: ' . $pkg['CurrentStock']);
                        $this->redirect('production');
                        return;
                    }
                    $pkgModel->updateStock($pkgMat, -$qty);
                }

                $this->model->create([
                    'BatchID' => $id, 'BatchNumber' => $batchNum,
                    'ProductionDate' => $this->getInput('production_date'),
                    'Flavour' => $this->getInput('flavour'), 'Quantity' => $qty,
                    'Unit' => $this->getInput('unit', 'litres'),
                    'Status' => $status,
                    'UserID' => $_SESSION['user_id'] ?? null,
                    'RawMaterialID' => $rawMat ?: null,
                    'PackagingMaterialID' => $pkgMat ?: null,
                    'MachineID' => $this->getInput('machine_id') ?: null,
                    'Notes' => $this->getInput('notes'),
                ]);

                // A batch completed straight from the form feeds finished goods too.
                if ($status === 'Completed') {
                    $this->syncFinishedGoods((array)$this->model->find($id));
                }

                $db->commit();
                logAudit($_SESSION['user_id'], 'CREATE', 'Production', $id, "Created batch $batchNum");
                setFlash('success', 'Batch created successfully.' . ($status === 'Completed' ? ' Finished goods inventory updated automatically.' : ''));
            } catch (\Exception $e) {
                $db->rollBack();
                error_log('Production create failed: ' . $e->getMessage());
                setFlash('error', 'Failed to create batch. Please try again.');
            }
            $this->redirect('production');
            return;
        }
        $this->render('form', [
            'rawMaterials' => (new RawMaterialModel())->all(),
            'packagingMaterials' => (new PackagingMaterialModel())->all(),
            'machines' => (new MachineModel())->all(),
            'users' => (new UserModel())->all(),
            'suggestedBatchNumber' => $this->model->suggestBatchNumber(),
        ]);
    }

    public function edit(): void {
        $id = $this->getInput('id');
        $batch = $this->model->find($id);
        if (!$batch) { setFlash('error', 'Not found.'); $this->redirect('production'); return; }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldStatus = (string)$batch['Status'];
            $newStatus = $this->getInput('status', $oldStatus);
            $qty = (float)$this->getInput('quantity', (string)$batch['Quantity']);

            // Mirror the creation rule ("Cancelled batches consume nothing"):
            // cancelling releases the materials deducted when the batch was
            // recorded; un-cancelling takes them out again.
            $db = getDb();
            $db->beginTransaction();
            try {
                if ($oldStatus !== 'Cancelled' && $newStatus === 'Cancelled') {
                    if ($batch['RawMaterialID']) {
                        (new RawMaterialModel())->updateStock((string)$batch['RawMaterialID'], (float)$batch['Quantity']);
                    }
                    if ($batch['PackagingMaterialID']) {
                        (new PackagingMaterialModel())->updateStock((string)$batch['PackagingMaterialID'], (float)$batch['Quantity']);
                    }
                }

                if ($oldStatus === 'Cancelled' && $newStatus !== 'Cancelled') {
                    $rawMat = $this->getInput('raw_material_id');
                    $pkgMat = $this->getInput('packaging_material_id');

                    if ($rawMat) {
                        $rmModel = new RawMaterialModel();
                        $rm = $rmModel->find($rawMat);
                        if ($rm && (float)$rm['CurrentStock'] < $qty) {
                            $db->rollBack();
                            setFlash('error', 'Insufficient raw material stock. Available: ' . $rm['CurrentStock']);
                            $this->redirect('production');
                            return;
                        }
                        $rmModel->updateStock($rawMat, -$qty);
                    }
                    if ($pkgMat) {
                        $pkgModel = new PackagingMaterialModel();
                        $pkg = $pkgModel->find($pkgMat);
                        if ($pkg && (float)$pkg['CurrentStock'] < $qty) {
                            $db->rollBack();
                            setFlash('error', 'Insufficient packaging material stock. Available: ' . $pkg['CurrentStock']);
                            $this->redirect('production');
                            return;
                        }
                        $pkgModel->updateStock($pkgMat, -$qty);
                    }
                }

                $this->model->update($id, [
                    'Flavour' => $this->getInput('flavour'),
                    'Quantity' => $qty,
                    'Status' => $newStatus,
                    'RawMaterialID' => $this->getInput('raw_material_id') ?: null,
                    'PackagingMaterialID' => $this->getInput('packaging_material_id') ?: null,
                    'MachineID' => $this->getInput('machine_id') ?: null,
                    'Notes' => $this->getInput('notes'),
                ]);

                // Keep finished goods inventory in step with the batch lifecycle.
                if ($newStatus === 'Completed' && $oldStatus !== 'Completed') {
                    $this->syncFinishedGoods((array)$this->model->find($id));
                } elseif ($oldStatus === 'Completed' && $newStatus !== 'Completed') {
                    if (!$this->releaseFinishedGoods($id)) {
                        $db->rollBack();
                        setFlash('error', 'Cannot un-complete this batch: sales orders reference its finished goods. Cancel those orders first.');
                        $this->redirect('production');
                        return;
                    }
                } elseif ($newStatus === 'Completed' && (float)$qty !== (float)$batch['Quantity']) {
                    // Quantity edited while completed: adjust the linked FG record.
                    $this->adjustFinishedGoods($id, (float)$qty - (float)$batch['Quantity']);
                }

                $db->commit();
                logAudit($_SESSION['user_id'], 'UPDATE', 'Production', $id, 'Updated batch');
                setFlash('success', 'Batch updated.');
            } catch (\Exception $e) {
                $db->rollBack();
                error_log('Production update failed: ' . $e->getMessage());
                setFlash('error', 'Failed to update batch. Please try again.');
            }
            $this->redirect('production');
            return;
        }
        $this->render('form', [
            'batch' => $batch,
            'rawMaterials' => (new RawMaterialModel())->all(),
            'packagingMaterials' => (new PackagingMaterialModel())->all(),
            'machines' => (new MachineModel())->all(),
            'users' => (new UserModel())->all(),
        ]);
    }

    public function delete(): void {
        $id = $this->getInput('id');
        $batch = $this->model->find($id);
        if (!$batch) { setFlash('error', 'Not found.'); $this->redirect('production'); return; }

        $db = getDb();
        $db->beginTransaction();
        try {
            // A batch with sold finished goods cannot simply disappear.
            if ($batch['Status'] !== 'Cancelled' && !$this->releaseFinishedGoods($id)) {
                $db->rollBack();
                setFlash('error', 'Cannot delete this batch: sales orders reference its finished goods. Cancel those orders first.');
                $this->redirect('production');
                return;
            }
            // A deleted batch that still held materials gives them back.
            if ($batch && $batch['Status'] !== 'Cancelled') {
                if ($batch['RawMaterialID']) {
                    (new RawMaterialModel())->updateStock((string)$batch['RawMaterialID'], (float)$batch['Quantity']);
                }
                if ($batch['PackagingMaterialID']) {
                    (new PackagingMaterialModel())->updateStock((string)$batch['PackagingMaterialID'], (float)$batch['Quantity']);
                }
            }
            $this->model->delete($id);
            $db->commit();
            logAudit($_SESSION['user_id'], 'DELETE', 'Production', $id, 'Deleted batch');
            setFlash('success', 'Batch deleted.');
        } catch (\Exception $e) {
            $db->rollBack();
            error_log('Production delete failed: ' . $e->getMessage());
            setFlash('error', 'Failed to delete batch. Please try again.');
        }
        $this->redirect('production');
    }

    /**
     * Printable batch label (barcode + flavour + dates). Rendered standalone,
     * outside the admin layout, and opens the browser print dialog.
     */
    public function label(): void {
        $batch = $this->model->find($this->getInput('id'));
        if (!$batch) { setFlash('error', 'Batch not found.'); $this->redirect('production'); return; }

        $fg = getDb()->prepare("SELECT ExpiryDate FROM finished_goods WHERE BatchID = ?");
        $fg->execute([(string)$batch['BatchID']]);
        $expiry = $fg->fetchColumn();

        $assetBase = appBaseUrl();
        header('Content-Type: text/html; charset=UTF-8');
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Batch Label - <?= sanitize((string)$batch['BatchNumber']) ?></title>
<style>
    body { font-family: Inter, Arial, sans-serif; background: #f1f5f9; display: flex; flex-direction: column; align-items: center; gap: 16px; padding: 32px 16px; margin: 0; }
    .label { background: #fff; border: 2px solid #0f172a; border-radius: 12px; padding: 20px 26px; width: 380px; }
    .label h1 { font-size: 1.05rem; margin: 0 0 2px; display: flex; align-items: center; gap: 8px; }
    .brand-dot { width: 14px; height: 14px; border-radius: 4px; background: linear-gradient(135deg, #22c55e, #06b6d4); display: inline-block; }
    .flavour { font-size: 1.3rem; font-weight: 800; margin: 6px 0 10px; }
    table { width: 100%; border-collapse: collapse; font-size: 0.82rem; }
    td { padding: 3px 0; vertical-align: top; }
    td.k { color: #475569; width: 42%; }
    .barcode { margin-top: 12px; text-align: center; }
    .actions { display: flex; gap: 8px; }
    button { padding: 8px 18px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .print { background: #16a34a; color: #fff; }
    .back { background: #e2e8f0; color: #0f172a; text-decoration: none; display: inline-flex; align-items: center; padding: 8px 18px; }
    @media print { body { background: #fff; padding: 0; } .actions { display: none; } .label { border-width: 1px; } }
</style>
</head>
<body onload="window.print()">
<div class="label">
    <h1><span class="brand-dot"></span> <?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></h1>
    <div class="flavour"><?= sanitize((string)$batch['Flavour']) ?> Juice</div>
    <table>
        <tr><td class="k">Batch Number</td><td><strong><?= sanitize((string)$batch['BatchNumber']) ?></strong></td></tr>
        <tr><td class="k">Production Date</td><td><?= sanitize((string)$batch['ProductionDate']) ?></td></tr>
        <tr><td class="k">Expiry Date</td><td><?= sanitize((string)($expiry ?: date('Y-m-d', strtotime((string)$batch['ProductionDate'] . ' +6 months')))) ?></td></tr>
        <tr><td class="k">Net Quantity</td><td><?= number_format((float)$batch['Quantity'], 2) ?> <?= sanitize((string)($batch['Unit'] ?? '')) ?></td></tr>
        <tr><td class="k">Status</td><td><?= sanitize((string)$batch['Status']) ?></td></tr>
    </table>
    <div class="barcode"><svg id="barcode"></svg></div>
</div>
<div class="actions">
    <button class="print" onclick="window.print()">Print Label</button>
    <a class="back" href="?route=production">Back to Batches</a>
</div>
<script src="<?= $assetBase ?>/frontend/assets/vendor/jsbarcode/JsBarcode.all.min.js"></script>
<script>
    if (typeof JsBarcode !== 'undefined') {
        JsBarcode('#barcode', <?= json_encode((string)$batch['BatchNumber']) ?>, { format: 'CODE128', height: 50, fontSize: 14, margin: 4 });
    } else {
        document.querySelector('.barcode').textContent = <?= json_encode((string)$batch['BatchNumber']) ?>;
    }
</script>
</body>
</html>
        <?php
        exit;
    }

    /** Upsert the finished-goods record for a completed batch. */
    private function syncFinishedGoods(array $batch): void {
        require_once __DIR__ . '/../models/FinishedGoodModel.php';
        $fgModel = new FinishedGoodModel();
        $existing = $fgModel->queryOne("SELECT * FROM finished_goods WHERE BatchID = ?", [$batch['BatchID']]);

        $expiry = date('Y-m-d', strtotime((string)$batch['ProductionDate'] . ' +6 months'));
        if ($existing) {
            $fgModel->update((string)$existing['FG_ID'], [
                'Flavour' => $batch['Flavour'],
                'QuantityAvailable' => (float)$batch['Quantity'],
                'Unit' => (string)($batch['Unit'] ?? 'bottles'),
                'ExpiryDate' => $expiry,
            ]);
            logAudit($_SESSION['user_id'] ?? null, 'UPDATE', 'Finished Goods', (string)$existing['FG_ID'], "Synced from completed batch {$batch['BatchNumber']}");
            return;
        }

        $fgId = generateId('FG');
        $fgModel->create([
            'FG_ID' => $fgId,
            'BatchID' => (string)$batch['BatchID'],
            'Flavour' => (string)$batch['Flavour'],
            'ExpiryDate' => $expiry,
            'QuantityAvailable' => (float)$batch['Quantity'],
            'Unit' => (string)($batch['Unit'] ?? 'bottles'),
        ]);
        logAudit($_SESSION['user_id'] ?? null, 'CREATE', 'Finished Goods', $fgId, "Auto-created from completed batch {$batch['BatchNumber']}");
    }

    /**
     * Remove finished goods tied to a batch (un-completing / deleting).
     * Returns false when sales orders depend on them -- caller aborts.
     */
    private function releaseFinishedGoods(string $batchId): bool {
        require_once __DIR__ . '/../models/FinishedGoodModel.php';
        $fgModel = new FinishedGoodModel();
        $goods = $fgModel->query(
            "SELECT fg.FG_ID,
                    (SELECT COUNT(*) FROM sales_orders so WHERE so.FG_ID = fg.FG_ID AND so.Status != 'Cancelled') AS Orders
             FROM finished_goods fg WHERE fg.BatchID = ?",
            [$batchId]
        );
        foreach ($goods as $g) {
            if ((int)$g['Orders'] > 0) return false;
        }
        foreach ($goods as $g) {
            $fgModel->delete((string)$g['FG_ID']);
            logAudit($_SESSION['user_id'] ?? null, 'DELETE', 'Finished Goods', (string)$g['FG_ID'], "Released with batch $batchId");
        }
        return true;
    }

    /** Apply a signed quantity delta to a completed batch's FG stock. */
    private function adjustFinishedGoods(string $batchId, float $delta): void {
        require_once __DIR__ . '/../models/FinishedGoodModel.php';
        $fgModel = new FinishedGoodModel();
        $existing = $fgModel->queryOne("SELECT * FROM finished_goods WHERE BatchID = ?", [$batchId]);
        if (!$existing) return;
        $newQty = max(0, (float)$existing['QuantityAvailable'] + $delta);
        $fgModel->update((string)$existing['FG_ID'], ['QuantityAvailable' => $newQty]);
        logAudit($_SESSION['user_id'] ?? null, 'UPDATE', 'Finished Goods', (string)$existing['FG_ID'], "Adjusted " . ($delta >= 0 ? '+' : '') . $delta . " from batch edit");
    }
}
