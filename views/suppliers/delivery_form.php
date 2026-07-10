<?php $pageTitle = isset($delivery) ? 'Edit Delivery' : 'New Delivery'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-box-seam me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=suppliers/deliveries" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($delivery) ? 'suppliers/delivery/edit&id=' . urlencode($delivery['DeliveryID']) : 'suppliers/delivery/create' ?>" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Supplier <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">Select Supplier</option>
                    <?php foreach ($suppliers as $s): ?>
                    <option value="<?= sanitize($s['SupplierID']) ?>" <?= (isset($delivery) && ($delivery['SupplierID'] ?? '') === $s['SupplierID']) ? 'selected' : '' ?>><?= sanitize($s['Name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                <input type="text" name="item_name" class="form-control" value="<?= sanitize($delivery['ItemName'] ?? $delivery['material_name'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="quantity" class="form-control" value="<?= sanitize((string)($delivery['Quantity'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="kg" <?= (isset($delivery) && ($delivery['Unit'] ?? '') === 'kg') ? 'selected' : '' ?>>kg</option>
                    <option value="litres" <?= (isset($delivery) && ($delivery['Unit'] ?? '') === 'litres') ? 'selected' : '' ?>>litres</option>
                    <option value="pcs" <?= (isset($delivery) && ($delivery['Unit'] ?? '') === 'pcs') ? 'selected' : '' ?>>pcs</option>
                    <option value="boxes" <?= (isset($delivery) && ($delivery['Unit'] ?? '') === 'boxes') ? 'selected' : '' ?>>boxes</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Delivery Date <span class="text-danger">*</span></label>
                <input type="date" name="delivery_date" class="form-control" value="<?= sanitize($delivery['DeliveryDate'] ?? $delivery['delivery_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quality Grade</label>
                <select name="quality_grade" class="form-select">
                    <option value="Grade A" <?= (isset($delivery) && ($delivery['QualityGrade'] ?? '') === 'Grade A') ? 'selected' : '' ?>>Grade A</option>
                    <option value="Grade B" <?= (isset($delivery) && ($delivery['QualityGrade'] ?? '') === 'Grade B') ? 'selected' : '' ?>>Grade B</option>
                    <option value="Grade C" <?= (isset($delivery) && ($delivery['QualityGrade'] ?? '') === 'Grade C') ? 'selected' : '' ?>>Grade C</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Received" <?= (isset($delivery) && ($delivery['Status'] ?? '') === 'Received') ? 'selected' : '' ?>>Received</option>
                    <option value="Pending" <?= (isset($delivery) && ($delivery['Status'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="Rejected" <?= (isset($delivery) && ($delivery['Status'] ?? '') === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Received By</label>
                <input type="text" name="received_by" class="form-control" value="<?= sanitize($delivery['ReceivedBy'] ?? $delivery['received_by'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= sanitize($delivery['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($delivery) ? 'Update' : 'Create' ?> Delivery</button>
                <a href="?route=suppliers/deliveries" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
