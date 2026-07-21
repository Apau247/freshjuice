<?php $pageTitle = isset($drill) ? 'Edit Drill' : 'Schedule Drill'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-fire me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=safety/drills" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($drill) ? 'safety/drills/edit&id=' . urlencode($drill['DrillID']) : 'safety/drills/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Drill Type <span class="text-danger">*</span></label>
                <input type="text" name="DrillType" class="form-control" value="<?= sanitize($drill['DrillType'] ?? '') ?>" required placeholder="e.g. Fire, Evacuation, Earthquake">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Drill Date <span class="text-danger">*</span></label>
                <input type="date" name="DrillDate" class="form-control" value="<?= sanitize($drill['DrillDate'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                <input type="text" name="Location" class="form-control" value="<?= sanitize($drill['Location'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Participants</label>
                <input type="number" name="ParticipantsCount" class="form-control" value="<?= sanitize((string)($drill['ParticipantsCount'] ?? '')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Duration (min)</label>
                <input type="number" name="DurationMinutes" class="form-control" value="<?= sanitize((string)($drill['DurationMinutes'] ?? '')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Conducted By</label>
                <input type="text" name="ConductedBy" class="form-control" value="<?= sanitize($drill['ConductedBy'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Outcome</label>
                <textarea name="Outcome" class="form-control" rows="2"><?= sanitize($drill['Outcome'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Issues Found</label>
                <textarea name="IssuesFound" class="form-control" rows="2"><?= sanitize($drill['IssuesFound'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Corrective Actions</label>
                <textarea name="CorrectiveAction" class="form-control" rows="2"><?= sanitize($drill['CorrectiveAction'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="Status" class="form-select" required>
                    <?php $st = $drill['Status'] ?? 'Scheduled'; ?>
                    <option value="Scheduled" <?= $st === 'Scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="Completed" <?= $st === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= $st === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($drill) ? 'Update' : 'Create' ?> Drill</button>
                <a href="?route=safety/drills" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
