<?php $pageTitle = isset($record) ? 'Edit Water Usage' : 'Add Water Usage'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-water me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=water" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($record) ? 'water/usage/edit&id=' . urlencode($record['WaterUsageID']) : 'water/usage/create' ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="<?= sanitize($record['Date'] ?? $record['date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Usage Type <span class="text-danger">*</span></label>
                <select name="usage_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Production" <?= (isset($record) && ($record['UsageType'] ?? '') === 'Production') ? 'selected' : '' ?>>Production</option>
                    <option value="Cleaning" <?= (isset($record) && ($record['UsageType'] ?? '') === 'Cleaning') ? 'selected' : '' ?>>Cleaning</option>
                    <option value="Cooling" <?= (isset($record) && ($record['UsageType'] ?? '') === 'Cooling') ? 'selected' : '' ?>>Cooling</option>
                    <option value="Sanitation" <?= (isset($record) && ($record['UsageType'] ?? '') === 'Sanitation') ? 'selected' : '' ?>>Sanitation</option>
                    <option value="Domestic" <?= (isset($record) && ($record['UsageType'] ?? '') === 'Domestic') ? 'selected' : '' ?>>Domestic</option>
                    <option value="Wastewater" <?= (isset($record) && ($record['UsageType'] ?? '') === 'Wastewater') ? 'selected' : '' ?>>Wastewater</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="quantity" class="form-control" value="<?= sanitize((string)($record['Quantity'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="litres" <?= (isset($record) && ($record['Unit'] ?? '') === 'litres') ? 'selected' : '' ?>>litres</option>
                    <option value="cubic_m" <?= (isset($record) && ($record['Unit'] ?? '') === 'cubic_m') ? 'selected' : '' ?>>cubic m</option>
                    <option value="gallons" <?= (isset($record) && ($record['Unit'] ?? '') === 'gallons') ? 'selected' : '' ?>>gallons</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Purpose</label>
                <input type="text" name="purpose" class="form-control" value="<?= sanitize($record['Purpose'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($record) ? 'Update' : 'Save' ?> Record</button>
                <a href="?route=water" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
