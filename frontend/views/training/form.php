<?php $pageTitle = isset($training) ? 'Edit Training' : 'Add Training'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-mortarboard me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=training" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($training) ? 'training/edit&id=' . urlencode($training['TrainingID']) : 'training/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Staff <span class="text-danger">*</span></label>
                <select name="StaffID" class="form-select" required>
                    <option value="">Select Staff Member</option>
                    <?php if (!empty($staffList)): foreach ($staffList as $staff): ?>
                    <option value="<?= sanitize($staff['StaffID']) ?>" <?= (isset($training) && ($training['EmployeeName'] ?? '') === $staff['StaffID']) ? 'selected' : '' ?>><?= sanitize($staff['FullName']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Training Type <span class="text-danger">*</span></label>
                <input type="text" name="TrainingType" class="form-control" value="<?= sanitize($training['TrainingType'] ?? '') ?>" required placeholder="e.g. Safety, Quality, Equipment">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Training Date <span class="text-danger">*</span></label>
                <input type="date" name="TrainingDate" class="form-control" value="<?= sanitize($training['TrainingDate'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Duration</label>
                <input type="text" name="Duration" class="form-control" value="<?= sanitize($training['Score'] ?? '') ?>" placeholder="e.g. 2 hours">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Cert Expiry Date</label>
                <input type="date" name="ExpiryDate" class="form-control" value="<?= sanitize($training['CertificationExpiry'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="Status" class="form-select" required>
                    <?php $st = $training['CompletionStatus'] ?? ''; ?>
                    <option value="Scheduled" <?= $st === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="In Progress" <?= $st === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $st === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= $st === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="Notes" class="form-control" rows="3"><?= sanitize($training['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($training) ? 'Update' : 'Create' ?> Training</button>
                <a href="?route=training" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
