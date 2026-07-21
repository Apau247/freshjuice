<?php $pageTitle = isset($material) ? 'Edit Packaging Material' : 'New Packaging Material'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-box me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=materials/packaging" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($material) ? 'materials/packaging/edit&id=' . urlencode($material['PackageID']) : 'materials/packaging/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Package Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= sanitize($material['Name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Type</label>
                <select name="type" class="form-select">
                    <option value="">Select Type</option>
                    <option value="Bottle" <?= (isset($material) && ($material['Type'] ?? '') === 'Bottle') ? 'selected' : '' ?>>Bottle</option>
                    <option value="Cap" <?= (isset($material) && ($material['Type'] ?? '') === 'Cap') ? 'selected' : '' ?>>Cap</option>
                    <option value="Label" <?= (isset($material) && ($material['Type'] ?? '') === 'Label') ? 'selected' : '' ?>>Label</option>
                    <option value="Box" <?= (isset($material) && ($material['Type'] ?? '') === 'Box') ? 'selected' : '' ?>>Box</option>
                    <option value="Film" <?= (isset($material) && ($material['Type'] ?? '') === 'Film') ? 'selected' : '' ?>>Film</option>
                    <option value="Other" <?= (isset($material) && ($material['Type'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="pcs" <?= (isset($material) && ($material['Unit'] ?? '') === 'pcs') ? 'selected' : '' ?>>pcs</option>
                    <option value="boxes" <?= (isset($material) && ($material['Unit'] ?? '') === 'boxes') ? 'selected' : '' ?>>boxes</option>
                    <option value="kg" <?= (isset($material) && ($material['Unit'] ?? '') === 'kg') ? 'selected' : '' ?>>kg</option>
                    <option value="rolls" <?= (isset($material) && ($material['Unit'] ?? '') === 'rolls') ? 'selected' : '' ?>>rolls</option>
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
            <div class="col-12">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= (isset($material) && ($material['Status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= (isset($material) && ($material['Status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($material) ? 'Update' : 'Create' ?> Packaging</button>
                <a href="?route=materials/packaging" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
