<?php $isEdit = isset($shift); $pageTitle = $isEdit ? 'Edit Shift' : 'New Shift'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-clock me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=staff/shifts" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '?route=staff/shifts/edit&id=' . urlencode($shift['ShiftID']) : '?route=staff/shifts/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Shift Name <span class="text-danger">*</span></label>
                <input type="text" name="shift_name" class="form-control" value="<?= sanitize($shift['ShiftName'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                <input type="time" name="start_time" class="form-control" value="<?= sanitize($shift['StartTime'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                <input type="time" name="end_time" class="form-control" value="<?= sanitize($shift['EndTime'] ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description</label>
                <input type="text" name="description" class="form-control" value="<?= sanitize($shift['Description'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= $isEdit ? 'Update' : 'Create' ?> Shift</button>
                <a href="?route=staff/shifts" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
