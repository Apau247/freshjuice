<?php $pageTitle = isset($machine) ? 'Edit Machine' : 'New Machine'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-cpu me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=machines" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($machine) ? 'machines/edit&id=' . urlencode($machine['MachineID']) : 'machines/create' ?>" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Machine Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= sanitize($machine['Name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Type</label>
                <select name="type" class="form-select">
                    <option value="">Select Type</option>
                    <option value="Juicer" <?= (isset($machine) && ($machine['Type'] ?? '') === 'Juicer') ? 'selected' : '' ?>>Juicer</option>
                    <option value="Pasteurizer" <?= (isset($machine) && ($machine['Type'] ?? '') === 'Pasteurizer') ? 'selected' : '' ?>>Pasteurizer</option>
                    <option value="Filler" <?= (isset($machine) && ($machine['Type'] ?? '') === 'Filler') ? 'selected' : '' ?>>Filler</option>
                    <option value="Capper" <?= (isset($machine) && ($machine['Type'] ?? '') === 'Capper') ? 'selected' : '' ?>>Capper</option>
                    <option value="Labeler" <?= (isset($machine) && ($machine['Type'] ?? '') === 'Labeler') ? 'selected' : '' ?>>Labeler</option>
                    <option value="Conveyor" <?= (isset($machine) && ($machine['Type'] ?? '') === 'Conveyor') ? 'selected' : '' ?>>Conveyor</option>
                    <option value="Packaging" <?= (isset($machine) && ($machine['Type'] ?? '') === 'Packaging') ? 'selected' : '' ?>>Packaging</option>
                    <option value="Other" <?= (isset($machine) && ($machine['Type'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Location</label>
                <input type="text" name="location" class="form-control" value="<?= sanitize($machine['Location'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Install Date</label>
                <input type="date" name="install_date" class="form-control" value="<?= sanitize($machine['InstallDate'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Last Service Date</label>
                <input type="date" name="last_service" class="form-control" value="<?= sanitize($machine['LastService'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Operational" <?= (isset($machine) && ($machine['Status'] ?? '') === 'Operational') ? 'selected' : '' ?>>Operational</option>
                    <option value="Maintenance" <?= (isset($machine) && ($machine['Status'] ?? '') === 'Maintenance') ? 'selected' : '' ?>>Maintenance</option>
                    <option value="Down" <?= (isset($machine) && ($machine['Status'] ?? '') === 'Down') ? 'selected' : '' ?>>Down</option>
                    <option value="Decommissioned" <?= (isset($machine) && ($machine['Status'] ?? '') === 'Decommissioned') ? 'selected' : '' ?>>Decommissioned</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($machine) ? 'Update' : 'Create' ?> Machine</button>
                <a href="?route=machines" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
