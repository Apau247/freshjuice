<?php $pageTitle = 'Record Water Usage'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-droplet me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=water" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=water/usage/create" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Usage Type <span class="text-danger">*</span></label>
                <select name="usage_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Production">Production</option>
                    <option value="Cleaning">Cleaning</option>
                    <option value="Cooling">Cooling</option>
                    <option value="Sanitation">Sanitation</option>
                    <option value="Domestic">Domestic</option>
                    <option value="Wastewater">Wastewater</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="quantity" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <option value="litres">litres</option>
                    <option value="cubic_m">cubic m</option>
                    <option value="gallons">gallons</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Purpose</label>
                <input type="text" name="purpose" class="form-control">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Save Record</button>
                <a href="?route=water" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
