<?php $pageTitle = isset($record) ? 'Edit FAT Record' : 'New FAT Record'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-clipboard-check me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=fat" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($record) ? 'fat/edit&id=' . urlencode($record['FAT_ID']) : 'fat/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Machine <span class="text-danger">*</span></label>
                <select name="machine_id" class="form-select" required>
                    <option value="">Select Machine</option>
                    <?php if (!empty($machines)): foreach ($machines as $m): ?>
                    <option value="<?= sanitize($m['MachineID']) ?>" <?= (isset($record) && ($record['MachineID'] ?? '') === $m['MachineID']) ? 'selected' : '' ?>><?= sanitize($m['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Test Date <span class="text-danger">*</span></label>
                <input type="date" name="test_date" class="form-control" value="<?= sanitize($record['TestDate'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Test Type <span class="text-danger">*</span></label>
                <input type="text" name="test_type" class="form-control" value="<?= sanitize($record['TestType'] ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Expected Result</label>
                <textarea name="expected_result" class="form-control" rows="2"><?= sanitize($record['ExpectedResult'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Actual Result</label>
                <textarea name="actual_result" class="form-control" rows="2"><?= sanitize($record['ActualResult'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Result</label>
                <select name="result" class="form-select">
                    <?php $r = $record['Result'] ?? 'Pending'; ?>
                    <option value="Pending" <?= $r === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Pass" <?= $r === 'Pass' ? 'selected' : '' ?>>Pass</option>
                    <option value="Fail" <?= $r === 'Fail' ? 'selected' : '' ?>>Fail</option>
                    <option value="Conditional" <?= $r === 'Conditional' ? 'selected' : '' ?>>Conditional</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php $st = $record['Status'] ?? 'Pending'; ?>
                    <option value="Pending" <?= $st === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $st === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $st === 'Completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Defects Found</label>
                <input type="text" name="defects_found" class="form-control" value="<?= sanitize($record['DefectsFound'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= sanitize($record['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($record) ? 'Update' : 'Create' ?> FAT Record</button>
                <a href="?route=fat" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
