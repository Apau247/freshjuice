<?php $pageTitle = 'Record Power Usage'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-lightning me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=power" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=power/usage/create" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Source <span class="text-danger">*</span></label>
                <select name="source" class="form-select" required>
                    <option value="">Select Source</option>
                    <option value="Grid">Grid</option>
                    <option value="Generator">Generator</option>
                    <option value="Solar">Solar</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Consumption (kWh) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="consumption_kwh" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Cost ($)</label>
                <input type="number" step="0.01" name="cost" class="form-control" value="0">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Notes</label>
                <input type="text" name="notes" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Save Record</button>
                <a href="?route=power" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
