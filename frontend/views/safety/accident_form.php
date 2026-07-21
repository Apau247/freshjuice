<?php $pageTitle = isset($accident) ? 'Edit Incident Report' : 'Report Incident'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-ambulance me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=safety/accidents" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($accident) ? 'safety/accidents/edit&id=' . urlencode($accident['AccidentID']) : 'safety/accidents/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Incident Date & Time <span class="text-danger">*</span></label>
                <input type="datetime-local" name="IncidentDate" class="form-control" value="<?= sanitize($accident['AccidentDate'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Location <span class="text-danger">*</span></label>
                <input type="text" name="Location" class="form-control" value="<?= sanitize($accident['Location'] ?? '') ?>" required>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                <textarea name="Description" class="form-control" rows="3" required><?= sanitize($accident['Description'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Incident Type <span class="text-danger">*</span></label>
                <select name="IncidentType" class="form-select" required>
                    <?php $it = $accident['IncidentType'] ?? ''; ?>
                    <option value="">-- Select --</option>
                    <option value="Injury" <?= $it === 'Injury' ? 'selected' : '' ?>>Injury</option>
                    <option value="Property Damage" <?= $it === 'Property Damage' ? 'selected' : '' ?>>Property Damage</option>
                    <option value="Environmental" <?= $it === 'Environmental' ? 'selected' : '' ?>>Environmental</option>
                    <option value="Near Miss" <?= $it === 'Near Miss' ? 'selected' : '' ?>>Near Miss</option>
                    <option value="Other" <?= $it === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Injury Severity</label>
                <input type="text" name="Injuries" class="form-control" value="<?= sanitize($accident['Injuries'] ?? '') ?>" placeholder="e.g. Minor, Major, Fatal">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Reported By</label>
                <input type="text" class="form-control" value="<?= sanitize(currentUser()['name'] ?? '') ?>" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Root Cause</label>
                <textarea name="RootCause" class="form-control" rows="3"><?= sanitize($accident['RootCause'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Corrective Actions</label>
                <textarea name="CorrectiveAction" class="form-control" rows="3"><?= sanitize($accident['CorrectiveAction'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="Status" class="form-select" required>
                    <?php $st = $accident['Status'] ?? 'Reported'; ?>
                    <option value="Reported" <?= $st === 'Reported' ? 'selected' : '' ?>>Reported</option>
                    <option value="Under Investigation" <?= $st === 'Under Investigation' ? 'selected' : '' ?>>Under Investigation</option>
                    <option value="Closed" <?= $st === 'Closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($accident) ? 'Update' : 'Create' ?> Report</button>
                <a href="?route=safety/accidents" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
