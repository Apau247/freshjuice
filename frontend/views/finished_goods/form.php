<?php $pageTitle = isset($good) ? 'Edit Finished Good' : 'New Finished Good'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-cup-straw me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=finished-goods" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($good) ? 'finished-goods/edit&id=' . urlencode($good['FG_ID']) : 'finished-goods/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Batch</label>
                <select name="batch_id" class="form-select">
                    <option value="">Select Batch</option>
                    <?php if (isset($batches)): foreach ($batches as $b): ?>
                    <option value="<?= sanitize($b['BatchID']) ?>" <?= (isset($good) && ($good['BatchID'] ?? '') === $b['BatchID']) ? 'selected' : '' ?>><?= sanitize($b['BatchNumber'] ?? $b['BatchID']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Flavour <span class="text-danger">*</span></label>
                <select name="flavour" class="form-select" required>
                    <option value="">Select Flavour</option>
                    <?php foreach (flavour_options() as $f): ?>
                    <option value="<?= sanitize($f) ?>" <?= (isset($good) && ($good['Flavour'] ?? '') === $f) ? 'selected' : '' ?>><?= sanitize($f) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Expiry Date <span class="text-danger">*</span></label>
                <input type="date" name="expiry_date" class="form-control" value="<?= sanitize($good['ExpiryDate'] ?? $good['expiry_date'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity Available <span class="text-danger">*</span></label>
                <input type="number" min="0" step="0.01" name="quantity_available" class="form-control" value="<?= sanitize((string)($good['QuantityAvailable'] ?? '')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="bottles" <?= (isset($good) && ($good['Unit'] ?? '') === 'bottles') ? 'selected' : '' ?>>bottles</option>
                    <option value="litres" <?= (isset($good) && ($good['Unit'] ?? '') === 'litres') ? 'selected' : '' ?>>litres</option>
                    <option value="pcs" <?= (isset($good) && ($good['Unit'] ?? '') === 'pcs') ? 'selected' : '' ?>>pcs</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Storage Location</label>
                <input type="text" name="storage_location" class="form-control" value="<?= sanitize($good['StorageLocation'] ?? $good['storage_location'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($good) ? 'Update' : 'Create' ?> Finished Good</button>
                <a href="?route=finished-goods" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
