<?php $pageTitle = 'New Water Quality Test'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-droplet-half me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=water" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=water/test/create" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Test Date <span class="text-danger">*</span></label>
                <input type="date" name="test_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Test Type <span class="text-danger">*</span></label>
                <select name="test_type" class="form-select" required>
                    <option value="">Select Test</option>
                    <option value="Routine">Routine</option>
                    <option value="Comprehensive">Comprehensive</option>
                    <option value="Microbiological">Microbiological</option>
                    <option value="Chemical">Chemical</option>
                    <option value="pH Check">pH Check</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Result</label>
                <select name="result" class="form-select">
                    <option value="Pending">Pending</option>
                    <option value="Pass">Pass</option>
                    <option value="Fail">Fail</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">pH Level</label>
                <input type="number" step="0.01" name="ph_level" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Turbidity</label>
                <input type="number" step="0.01" name="turbidity" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">TDS</label>
                <input type="number" step="0.01" name="tds" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Chlorine</label>
                <input type="number" step="0.01" name="chlorine" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bacteria Count</label>
                <input type="number" step="0.01" name="bacteria_count" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tested By</label>
                <select name="tested_by" class="form-select">
                    <option value="">Select User</option>
                    <?php if (!empty($users)): foreach ($users as $u): ?>
                    <option value="<?= sanitize($u['UserID']) ?>"><?= sanitize($u['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Create Test</button>
                <a href="?route=water" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
