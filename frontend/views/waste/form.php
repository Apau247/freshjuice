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
                <?php
                // Cascade: each batch option carries its unit, so picking a
                // batch flips the Unit dropdown to match that batch.
                foreach ($batches as &$b) {
                    $b['display'] = trim(($b['BatchNumber'] ?? $b['BatchID']) . ' - ' . ($b['Flavour'] ?? ' -'));
                }
                unset($b);
                $batchAttrs = [];
                foreach ($batches as $b) {
                    $batchAttrs[(string)$b['BatchID']] = 'data-unit="' . sanitize((string)($b['Unit'] ?? '')) . '"';
                }
                ?>
                <select name="batch_id" class="form-select" data-unit-target="unit">
                    <?= trending_options($batches, 'BatchID', 'display', $trends['batch'] ?? null, isset($record) ? ($record['BatchID'] ?? null) : null, 'Select Batch', $batchAttrs) ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Waste Type <span class="text-danger">*</span></label>
                <select name="waste_type" class="form-select" required>
                    <?= trending_value_options(['Production', 'Packaging', 'Raw Material', 'Chemical', 'Water', 'Other'], $trends['type'] ?? null, isset($record) ? ($record['WasteType'] ?? null) : null, 'Select Type') ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" min="0" step="0.01" name="quantity" class="form-control" value="<?= sanitize((string)($record['Quantity'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <?= trending_value_options(['kg', 'litres', 'pcs', 'cubic_m'], $trends['unit'] ?? null, isset($record) ? ($record['Unit'] ?? null) : null) ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Disposal Method</label>
                <select name="disposal_method" class="form-select">
                    <?= trending_value_options(['Landfill', 'Recycling', 'Composting', 'Treatment', 'Incinerator'], $trends['disposal'] ?? null, isset($record) ? ($record['DisposalMethod'] ?? null) : null) ?>
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
