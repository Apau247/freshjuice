<?php $pageTitle = isset($inspection) ? 'Edit Safety Inspection' : 'New Safety Inspection'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-shield-check me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=safety" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($inspection) ? 'safety/edit&id=' . urlencode($inspection['SafetyID']) : 'safety/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Inspection Date <span class="text-danger">*</span></label>
                <input type="date" name="InspectionDate" class="form-control" value="<?= sanitize($inspection['InspectionDate'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Inspection Type <span class="text-danger">*</span></label>
                <select name="InspectionType" class="form-select" required>
                    <?php $it = $inspection['InspectionType'] ?? ''; ?>
                    <option value="General" <?= $it === 'General' ? 'selected' : '' ?>>General</option>
                    <option value="Fire Safety" <?= $it === 'Fire Safety' ? 'selected' : '' ?>>Fire Safety</option>
                    <option value="Electrical" <?= $it === 'Electrical' ? 'selected' : '' ?>>Electrical</option>
                    <option value="Chemical" <?= $it === 'Chemical' ? 'selected' : '' ?>>Chemical</option>
                    <option value="Machinery" <?= $it === 'Machinery' ? 'selected' : '' ?>>Machinery</option>
                    <option value="Hygiene" <?= $it === 'Hygiene' ? 'selected' : '' ?>>Hygiene</option>
                    <option value="Environmental" <?= $it === 'Environmental' ? 'selected' : '' ?>>Environmental</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Area <span class="text-danger">*</span></label>
                <input type="text" name="Area" class="form-control" value="<?= sanitize($inspection['Area'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Hazard Level</label>
                <select name="HazardLevel" class="form-select">
                    <?php $hl = $inspection['HazardLevel'] ?? 'Low'; ?>
                    <option value="Low" <?= $hl === 'Low' ? 'selected' : '' ?>>Low</option>
                    <option value="Medium" <?= $hl === 'Medium' ? 'selected' : '' ?>>Medium</option>
                    <option value="High" <?= $hl === 'High' ? 'selected' : '' ?>>High</option>
                    <option value="Critical" <?= $hl === 'Critical' ? 'selected' : '' ?>>Critical</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Responsible Person</label>
                <input type="text" name="ResponsiblePerson" class="form-control" value="<?= sanitize($inspection['ResponsiblePerson'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Target Date</label>
                <input type="date" name="TargetDate" class="form-control" value="<?= sanitize($inspection['TargetDate'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Findings</label>
                <textarea name="Findings" class="form-control" rows="3"><?= sanitize($inspection['Findings'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Corrective Action</label>
                <textarea name="CorrectiveAction" class="form-control" rows="3"><?= sanitize($inspection['CorrectiveAction'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="Status" class="form-select" required>
                    <?php $st = $inspection['Status'] ?? 'Open'; ?>
                    <option value="Open" <?= $st === 'Open' ? 'selected' : '' ?>>Open</option>
                    <option value="In Progress" <?= $st === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Closed" <?= $st === 'Closed' ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($inspection) ? 'Update' : 'Create' ?> Inspection</button>
                <a href="?route=safety" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
