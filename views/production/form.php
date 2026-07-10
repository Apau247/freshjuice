<?php $pageTitle = isset($batch) ? 'Edit Production Batch' : 'New Production Batch'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=production" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($batch) ? 'production/edit&id=' . urlencode($batch['BatchID']) : 'production/create' ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Batch Number <span class="text-danger">*</span></label>
                <input type="text" name="batch_number" class="form-control" value="<?= sanitize($batch['BatchNumber'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Production Date <span class="text-danger">*</span></label>
                <input type="date" name="production_date" class="form-control" value="<?= sanitize($batch['ProductionDate'] ?? $batch['production_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Flavour <span class="text-danger">*</span></label>
                <select name="flavour" class="form-select" required>
                    <option value="">Select Flavour</option>
                    <option value="Orange" <?= (isset($batch) && ($batch['Flavour'] ?? '') === 'Orange') ? 'selected' : '' ?>>Orange</option>
                    <option value="Apple" <?= (isset($batch) && ($batch['Flavour'] ?? '') === 'Apple') ? 'selected' : '' ?>>Apple</option>
                    <option value="Mango" <?= (isset($batch) && ($batch['Flavour'] ?? '') === 'Mango') ? 'selected' : '' ?>>Mango</option>
                    <option value="Grape" <?= (isset($batch) && ($batch['Flavour'] ?? '') === 'Grape') ? 'selected' : '' ?>>Grape</option>
                    <option value="Mixed Fruit" <?= (isset($batch) && ($batch['Flavour'] ?? '') === 'Mixed Fruit') ? 'selected' : '' ?>>Mixed Fruit</option>
                    <option value="Lemon" <?= (isset($batch) && ($batch['Flavour'] ?? '') === 'Lemon') ? 'selected' : '' ?>>Lemon</option>
                    <option value="Pineapple" <?= (isset($batch) && ($batch['Flavour'] ?? '') === 'Pineapple') ? 'selected' : '' ?>>Pineapple</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="quantity" class="form-control" value="<?= sanitize((string)($batch['Quantity'] ?? '')) ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="litres" <?= (isset($batch) && ($batch['Unit'] ?? '') === 'litres') ? 'selected' : '' ?>>litres</option>
                    <option value="kg" <?= (isset($batch) && ($batch['Unit'] ?? '') === 'kg') ? 'selected' : '' ?>>kg</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Pending" <?= (isset($batch) && ($batch['Status'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= (isset($batch) && ($batch['Status'] ?? '') === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= (isset($batch) && ($batch['Status'] ?? '') === 'Completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="Rejected" <?= (isset($batch) && ($batch['Status'] ?? '') === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                    <option value="Cancelled" <?= (isset($batch) && ($batch['Status'] ?? '') === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Operator / User</label>
                <select name="user_id" class="form-select">
                    <option value="">Select Operator</option>
                    <?php if (isset($users)): foreach ($users as $u): ?>
                    <option value="<?= sanitize($u['UserID']) ?>" <?= (isset($batch) && ($batch['UserID'] ?? '') === $u['UserID']) ? 'selected' : '' ?>><?= sanitize($u['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Raw Material</label>
                <select name="raw_material_id" class="form-select">
                    <option value="">Select RM</option>
                    <?php if (isset($rawMaterials)): foreach ($rawMaterials as $rm): ?>
                    <option value="<?= sanitize($rm['MaterialID']) ?>" <?= (isset($batch) && ($batch['RawMaterialID'] ?? '') === $rm['MaterialID']) ? 'selected' : '' ?>><?= sanitize($rm['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Packaging Material</label>
                <select name="packaging_material_id" class="form-select">
                    <option value="">Select PKG</option>
                    <?php if (isset($packagingMaterials)): foreach ($packagingMaterials as $pm): ?>
                    <option value="<?= sanitize($pm['PackageID']) ?>" <?= (isset($batch) && ($batch['PackagingMaterialID'] ?? '') === $pm['PackageID']) ? 'selected' : '' ?>><?= sanitize($pm['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Machine</label>
                <select name="machine_id" class="form-select">
                    <option value="">Select Machine</option>
                    <?php if (isset($machines)): foreach ($machines as $m): ?>
                    <option value="<?= sanitize($m['MachineID']) ?>" <?= (isset($batch) && ($batch['MachineID'] ?? '') === $m['MachineID']) ? 'selected' : '' ?>><?= sanitize($m['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= sanitize($batch['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($batch) ? 'Update' : 'Create' ?> Batch</button>
                <a href="?route=production" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
