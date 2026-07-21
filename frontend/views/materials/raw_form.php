<?php $pageTitle = isset($material) ? 'Edit Raw Material' : 'New Raw Material'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-bar-chart-steps me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=materials/raw" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($material) ? 'materials/raw/edit&id=' . urlencode($material['MaterialID']) : 'materials/raw/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Material Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= sanitize($material['Name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Type</label>
                <select name="type" class="form-select">
                    <option value="">Select Type</option>
                    <option value="Fruit" <?= (isset($material) && ($material['Type'] ?? '') === 'Fruit') ? 'selected' : '' ?>>Fruit</option>
                    <option value="Vegetable" <?= (isset($material) && ($material['Type'] ?? '') === 'Vegetable') ? 'selected' : '' ?>>Vegetable</option>
                    <option value="Sugar" <?= (isset($material) && ($material['Type'] ?? '') === 'Sugar') ? 'selected' : '' ?>>Sugar</option>
                    <option value="Additive" <?= (isset($material) && ($material['Type'] ?? '') === 'Additive') ? 'selected' : '' ?>>Additive</option>
                    <option value="Chemical" <?= (isset($material) && ($material['Type'] ?? '') === 'Chemical') ? 'selected' : '' ?>>Chemical</option>
                    <option value="Other" <?= (isset($material) && ($material['Type'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="kg" <?= (isset($material) && ($material['Unit'] ?? '') === 'kg') ? 'selected' : '' ?>>kg</option>
                    <option value="litres" <?= (isset($material) && ($material['Unit'] ?? '') === 'litres') ? 'selected' : '' ?>>litres</option>
                    <option value="pcs" <?= (isset($material) && ($material['Unit'] ?? '') === 'pcs') ? 'selected' : '' ?>>pcs</option>
                    <option value="boxes" <?= (isset($material) && ($material['Unit'] ?? '') === 'boxes') ? 'selected' : '' ?>>boxes</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Current Stock</label>
                <input type="number" step="0.01" name="current_stock" class="form-control" value="<?= sanitize((string)($material['CurrentStock'] ?? '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Min Stock Level</label>
                <input type="number" step="0.01" name="min_stock" class="form-control" value="<?= sanitize((string)($material['MinStock'] ?? '0')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Supplier</label>
                <select name="supplier_id" class="form-select">
                    <option value="">Select Supplier</option>
                    <?php if (isset($suppliers)): foreach ($suppliers as $s): ?>
                    <option value="<?= sanitize($s['SupplierID']) ?>" <?= (isset($material) && ($material['SupplierID'] ?? '') === $s['SupplierID']) ? 'selected' : '' ?>><?= sanitize($s['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= (isset($material) && ($material['Status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= (isset($material) && ($material['Status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($material) ? 'Update' : 'Create' ?> Material</button>
                <a href="?route=materials/raw" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
