<?php
declare(strict_types=1);
require_once __DIR__ . '/Controller.php';
require_once __DIR__ . '/../models/RawMaterialModel.php';
require_once __DIR__ . '/../models/PackagingMaterialModel.php';

/**
 * Inventory alert centre (SRS: Inventory Management -> Low Stock Alerts / Expiry Alerts).
 * Read-only pages: every section is additionally gated by the module the data
 * belongs to, so a user without e.g. finished_goods access never sees that block.
 */
class InventoryController extends Controller {
    public function __construct() {
        parent::__construct();
        $this->model = new RawMaterialModel();
        $this->viewPath = 'inventory';
    }

    public function lowStock(): void {
        $db = $this->model->getDb();

        $rawMaterials = [];
        $packaging    = [];

        if (can('materials')) {
            $rawMaterials = $db->query(
                "SELECT rm.*, s.Name AS SupplierName,
                        GREATEST(rm.MinStock * 2 - rm.CurrentStock, rm.MinStock) AS SuggestedReorder
                 FROM raw_materials rm
                 LEFT JOIN suppliers s ON rm.SupplierID = s.SupplierID
                 WHERE rm.CurrentStock <= rm.MinStock AND rm.Status = 'Active'
                 ORDER BY (rm.CurrentStock - rm.MinStock) ASC"
            )->fetchAll();

            $packaging = $db->query(
                "SELECT pm.*, GREATEST(pm.MinStock * 2 - pm.CurrentStock, pm.MinStock) AS SuggestedReorder
                 FROM packaging_materials pm
                 WHERE pm.CurrentStock <= pm.MinStock AND pm.Status = 'Active'
                 ORDER BY (pm.CurrentStock - pm.MinStock) ASC"
            )->fetchAll();
        }

        $this->render('low_stock', [
            'rawMaterials' => $rawMaterials,
            'packaging'    => $packaging,
        ]);
    }

    public function expiry(): void {
        $db = $this->model->getDb();

        $expiringGoods  = [];
        $expiredGoods   = [];
        $expiringCerts  = [];
        $expiredCerts   = [];
        $expiringPermits = [];

        if (can('finished_goods')) {
            $expiringGoods = $db->query(
                "SELECT fg.*, b.BatchNumber
                 FROM finished_goods fg
                 LEFT JOIN production_batches b ON fg.BatchID = b.BatchID
                 WHERE fg.ExpiryDate > CURDATE()
                   AND fg.ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                   AND fg.QuantityAvailable > 0
                 ORDER BY fg.ExpiryDate ASC"
            )->fetchAll();

            $expiredGoods = $db->query(
                "SELECT fg.*, b.BatchNumber
                 FROM finished_goods fg
                 LEFT JOIN production_batches b ON fg.BatchID = b.BatchID
                 WHERE fg.ExpiryDate <= CURDATE() AND fg.QuantityAvailable > 0
                 ORDER BY fg.ExpiryDate ASC"
            )->fetchAll();
        }

        if (can('certifications')) {
            $expiringCerts = $db->query(
                "SELECT * FROM certifications
                 WHERE ExpiryDate >= CURDATE()
                   AND ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
                   AND Status != 'Expired'
                 ORDER BY ExpiryDate ASC"
            )->fetchAll();

            $expiredCerts = $db->query(
                "SELECT * FROM certifications
                 WHERE ExpiryDate < CURDATE() OR Status = 'Expired'
                 ORDER BY ExpiryDate ASC"
            )->fetchAll();
        }

        if (can('permits')) {
            $expiringPermits = $db->query(
                "SELECT * FROM permits
                 WHERE ExpiryDate >= CURDATE()
                   AND ExpiryDate <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)
                   AND Status = 'Active'
                 ORDER BY ExpiryDate ASC"
            )->fetchAll();
        }

        $this->render('expiry', [
            'expiringGoods'   => $expiringGoods,
            'expiredGoods'    => $expiredGoods,
            'expiringCerts'   => $expiringCerts,
            'expiredCerts'    => $expiredCerts,
            'expiringPermits' => $expiringPermits,
        ]);
    }
}
