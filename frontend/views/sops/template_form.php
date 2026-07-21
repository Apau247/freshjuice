<?php $pageTitle = isset($template) ? 'Edit SOP Template' : 'New SOP Template'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-journal me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=sops" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($template) ? 'sops/template/edit&id=' . urlencode($template['SOP_ID']) : 'sops/template/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= sanitize($template['Title'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Version</label>
                <input type="text" name="version" class="form-control" value="<?= sanitize($template['Version'] ?? '1.0') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Department</label>
                <select name="department" class="form-select">
                    <option value="">Select Department</option>
                    <?php $dep = $template['Department'] ?? ''; ?>
                    <option value="Production" <?= $dep === 'Production' ? 'selected' : '' ?>>Production</option>
                    <option value="Quality" <?= $dep === 'Quality' ? 'selected' : '' ?>>Quality</option>
                    <option value="Maintenance" <?= $dep === 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    <option value="Logistics" <?= $dep === 'Logistics' ? 'selected' : '' ?>>Logistics</option>
                    <option value="Administration" <?= $dep === 'Administration' ? 'selected' : '' ?>>Administration</option>
                    <option value="Safety" <?= $dep === 'Safety' ? 'selected' : '' ?>>Safety</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Effective Date</label>
                <input type="date" name="effective_date" class="form-control" value="<?= sanitize($template['EffectiveDate'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Review Date</label>
                <input type="date" name="review_date" class="form-control" value="<?= sanitize($template['ReviewDate'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php $st = $template['Status'] ?? 'Active'; ?>
                    <option value="Active" <?= $st === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Under Review" <?= $st === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="Archived" <?= $st === 'Archived' ? 'selected' : '' ?>>Archived</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Content</label>
                <textarea name="content" class="form-control" rows="12"><?= sanitize($template['Content'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($template) ? 'Update' : 'Create' ?> Template</button>
                <a href="?route=sops" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
