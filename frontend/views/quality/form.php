<?php $pageTitle = isset($inspection) ? 'Edit Inspection' : 'New Quality Inspection'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-check-circle me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=quality" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($inspection) ? 'quality/edit&id=' . urlencode($inspection['InspectionID']) : 'quality/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Inspection Type <span class="text-danger">*</span></label>
                <select name="inspection_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Incoming" <?= (isset($inspection) && ($inspection['InspectionType'] ?? '') === 'Incoming') ? 'selected' : '' ?>>Incoming</option>
                    <option value="In-Process" <?= (isset($inspection) && ($inspection['InspectionType'] ?? '') === 'In-Process') ? 'selected' : '' ?>>In-Process</option>
                    <option value="Finished" <?= (isset($inspection) && ($inspection['InspectionType'] ?? '') === 'Finished') ? 'selected' : '' ?>>Finished</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Batch</label>
                <select name="batch_id" class="form-select">
                    <option value="">Select Batch</option>
                    <?php if (isset($batches)): foreach ($batches as $b): ?>
                    <option value="<?= sanitize($b['BatchID']) ?>" <?= (isset($inspection) && ($inspection['BatchID'] ?? '') === $b['BatchID']) ? 'selected' : '' ?>><?= sanitize($b['BatchNumber'] ?? $b['BatchID']) ?> - <?= sanitize($b['Flavour'] ?? '') ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Inspection Date <span class="text-danger">*</span></label>
                <input type="date" name="inspection_date" class="form-control" value="<?= sanitize($inspection['InspectionDate'] ?? $inspection['inspection_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Result</label>
                <select name="result" class="form-select">
                    <option value="Pending" <?= (isset($inspection) && ($inspection['Result'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="Pass" <?= (isset($inspection) && ($inspection['Result'] ?? '') === 'Pass') ? 'selected' : '' ?>>Pass</option>
                    <option value="Fail" <?= (isset($inspection) && ($inspection['Result'] ?? '') === 'Fail') ? 'selected' : '' ?>>Fail</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Open" <?= (isset($inspection) && ($inspection['Status'] ?? '') === 'Open') ? 'selected' : '' ?>>Open</option>
                    <option value="In Progress" <?= (isset($inspection) && ($inspection['Status'] ?? '') === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="Closed" <?= (isset($inspection) && ($inspection['Status'] ?? '') === 'Closed') ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Inspector</label>
                <select name="inspector_id" class="form-select">
                    <option value="">Select Inspector</option>
                    <?php if (isset($users)): foreach ($users as $u): ?>
                    <option value="<?= sanitize($u['UserID']) ?>" <?= (isset($inspection) && ($inspection['InspectorID'] ?? '') === $u['UserID']) ? 'selected' : '' ?>><?= sanitize($u['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Defects Found</label>
                <textarea name="defects_found" class="form-control" rows="3"><?= sanitize($inspection['DefectsFound'] ?? $inspection['defects_found'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Test Results</label>
                <textarea name="test_results" class="form-control" rows="3"><?= sanitize($inspection['TestResults'] ?? $inspection['test_results'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">CAPA (Corrective & Preventive Actions)</label>
                <textarea name="capa" class="form-control" rows="3"><?= sanitize($inspection['CAPA'] ?? $inspection['capa'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($inspection) ? 'Update' : 'Create' ?> Inspection</button>
                <a href="?route=quality" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
