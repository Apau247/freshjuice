<?php $isEdit = isset($record); $pageTitle = $isEdit ? 'Edit Water Usage' : 'Record Water Usage'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-droplet me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=water" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '?route=water/usage/edit&id=' . urlencode($record['WaterUsageID']) : '?route=water/usage/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="<?= sanitize($record['Date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Usage Type <span class="text-danger">*</span></label>
                <select name="usage_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <?php $ut = $record['UsageType'] ?? ''; ?>
                    <option value="Production" <?= $ut === 'Production' ? 'selected' : '' ?>>Production</option>
                    <option value="Cleaning" <?= $ut === 'Cleaning' ? 'selected' : '' ?>>Cleaning</option>
                    <option value="Cooling" <?= $ut === 'Cooling' ? 'selected' : '' ?>>Cooling</option>
                    <option value="Sanitation" <?= $ut === 'Sanitation' ? 'selected' : '' ?>>Sanitation</option>
                    <option value="Domestic" <?= $ut === 'Domestic' ? 'selected' : '' ?>>Domestic</option>
                    <option value="Wastewater" <?= $ut === 'Wastewater' ? 'selected' : '' ?>>Wastewater</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" min="0" step="0.01" name="quantity" class="form-control" value="<?= sanitize((string)($record['Quantity'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <?php $u = $record['Unit'] ?? 'litres'; ?>
                    <option value="litres" <?= $u === 'litres' ? 'selected' : '' ?>>litres</option>
                    <option value="cubic_m" <?= $u === 'cubic_m' ? 'selected' : '' ?>>cubic m</option>
                    <option value="gallons" <?= $u === 'gallons' ? 'selected' : '' ?>>gallons</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Purpose</label>
                <input type="text" name="purpose" class="form-control" value="<?= sanitize($record['Purpose'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= $isEdit ? 'Update' : 'Save' ?> Record</button>
                <a href="?route=water" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
