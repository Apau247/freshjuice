<?php $pageTitle = isset($test) ? 'Edit Water Quality Test' : 'New Water Quality Test'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-check2-square me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=water" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($test) ? 'water/test/edit&id=' . urlencode($test['WaterTestID']) : 'water/test/create' ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Test Date <span class="text-danger">*</span></label>
                <input type="date" name="test_date" class="form-control" value="<?= sanitize($test['TestDate'] ?? $test['test_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Test Type <span class="text-danger">*</span></label>
                <select name="test_type" class="form-select" required>
                    <option value="">Select Test</option>
                    <option value="Routine" <?= (isset($test) && ($test['TestType'] ?? '') === 'Routine') ? 'selected' : '' ?>>Routine</option>
                    <option value="Comprehensive" <?= (isset($test) && ($test['TestType'] ?? '') === 'Comprehensive') ? 'selected' : '' ?>>Comprehensive</option>
                    <option value="Microbiological" <?= (isset($test) && ($test['TestType'] ?? '') === 'Microbiological') ? 'selected' : '' ?>>Microbiological</option>
                    <option value="Chemical" <?= (isset($test) && ($test['TestType'] ?? '') === 'Chemical') ? 'selected' : '' ?>>Chemical</option>
                    <option value="pH Check" <?= (isset($test) && ($test['TestType'] ?? '') === 'pH Check') ? 'selected' : '' ?>>pH Check</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Result</label>
                <select name="result" class="form-select">
                    <option value="Pending" <?= (isset($test) && ($test['Result'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="Pass" <?= (isset($test) && ($test['Result'] ?? '') === 'Pass') ? 'selected' : '' ?>>Pass</option>
                    <option value="Fail" <?= (isset($test) && ($test['Result'] ?? '') === 'Fail') ? 'selected' : '' ?>>Fail</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">pH Level</label>
                <input type="number" step="0.01" name="ph_level" class="form-control" value="<?= sanitize((string)($test['pH_Level'] ?? $test['ph_level'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Turbidity</label>
                <input type="number" step="0.01" name="turbidity" class="form-control" value="<?= sanitize((string)($test['Turbidity'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">TDS</label>
                <input type="number" step="0.01" name="tds" class="form-control" value="<?= sanitize((string)($test['TDS'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Chlorine</label>
                <input type="number" step="0.01" name="chlorine" class="form-control" value="<?= sanitize((string)($test['Chlorine'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bacteria Count</label>
                <input type="number" step="0.01" name="bacteria_count" class="form-control" value="<?= sanitize((string)($test['BacteriaCount'] ?? '')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tested By</label>
                <select name="tested_by" class="form-select">
                    <option value="">Select User</option>
                    <?php if (isset($users)): foreach ($users as $u): ?>
                    <option value="<?= sanitize($u['UserID']) ?>" <?= (isset($test) && ($test['TestedBy'] ?? '') === $u['UserID']) ? 'selected' : '' ?>><?= sanitize($u['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= sanitize($test['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($test) ? 'Update' : 'Create' ?> Test</button>
                <a href="?route=water" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
