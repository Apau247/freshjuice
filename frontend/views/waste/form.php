<?php $pageTitle = isset($record) ? 'Edit Waste Record' : 'New Waste Record'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-trash me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=waste" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($record) ? 'waste/edit&id=' . urlencode($record['WasteID']) : 'waste/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="<?= sanitize($record['Date'] ?? $record['date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Batch</label>
                <select name="batch_id" class="form-select">
                    <option value="">Select Batch</option>
                    <?php if (isset($batches)): foreach ($batches as $b): ?>
                    <option value="<?= sanitize($b['BatchID']) ?>" <?= (isset($record) && ($record['BatchID'] ?? '') === $b['BatchID']) ? 'selected' : '' ?>><?= sanitize($b['BatchNumber'] ?? $b['BatchID']) ?> - <?= sanitize($b['Flavour'] ?? '') ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Waste Type <span class="text-danger">*</span></label>
                <select name="waste_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Production" <?= (isset($record) && ($record['WasteType'] ?? '') === 'Production') ? 'selected' : '' ?>>Production</option>
                    <option value="Packaging" <?= (isset($record) && ($record['WasteType'] ?? '') === 'Packaging') ? 'selected' : '' ?>>Packaging</option>
                    <option value="Raw Material" <?= (isset($record) && ($record['WasteType'] ?? '') === 'Raw Material') ? 'selected' : '' ?>>Raw Material</option>
                    <option value="Chemical" <?= (isset($record) && ($record['WasteType'] ?? '') === 'Chemical') ? 'selected' : '' ?>>Chemical</option>
                    <option value="Water" <?= (isset($record) && ($record['WasteType'] ?? '') === 'Water') ? 'selected' : '' ?>>Water</option>
                    <option value="Other" <?= (isset($record) && ($record['WasteType'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="quantity" class="form-control" value="<?= sanitize((string)($record['Quantity'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="kg" <?= (isset($record) && ($record['Unit'] ?? '') === 'kg') ? 'selected' : '' ?>>kg</option>
                    <option value="litres" <?= (isset($record) && ($record['Unit'] ?? '') === 'litres') ? 'selected' : '' ?>>litres</option>
                    <option value="pcs" <?= (isset($record) && ($record['Unit'] ?? '') === 'pcs') ? 'selected' : '' ?>>pcs</option>
                    <option value="cubic_m" <?= (isset($record) && ($record['Unit'] ?? '') === 'cubic_m') ? 'selected' : '' ?>>cubic m</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Disposal Method</label>
                <select name="disposal_method" class="form-select">
                    <option value="Landfill" <?= (isset($record) && ($record['DisposalMethod'] ?? '') === 'Landfill') ? 'selected' : '' ?>>Landfill</option>
                    <option value="Recycling" <?= (isset($record) && ($record['DisposalMethod'] ?? '') === 'Recycling') ? 'selected' : '' ?>>Recycling</option>
                    <option value="Composting" <?= (isset($record) && ($record['DisposalMethod'] ?? '') === 'Composting') ? 'selected' : '' ?>>Composting</option>
                    <option value="Treatment" <?= (isset($record) && ($record['DisposalMethod'] ?? '') === 'Treatment') ? 'selected' : '' ?>>Treatment</option>
                    <option value="Incinerator" <?= (isset($record) && ($record['DisposalMethod'] ?? '') === 'Incinerator') ? 'selected' : '' ?>>Incinerator</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Environmental Impact</label>
                <textarea name="environmental_impact" class="form-control" rows="2"><?= sanitize($record['EnvironmentalImpact'] ?? $record['environmental_impact'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($record) ? 'Update' : 'Create' ?> Record</button>
                <a href="?route=waste" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
