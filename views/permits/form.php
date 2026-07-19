<?php $pageTitle = isset($permit) ? 'Edit Permit' : 'Add Permit'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-medical me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=permits" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($permit) ? 'permits/edit&id=' . urlencode($permit['PermitID']) : 'permits/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Permit Type <span class="text-danger">*</span></label>
                <input type="text" name="PermitType" class="form-control" value="<?= sanitize($permit['PermitType'] ?? '') ?>" required placeholder="e.g. Fire Safety, Environmental, Operating">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Permit Number <span class="text-danger">*</span></label>
                <input type="text" name="PermitNumber" class="form-control" value="<?= sanitize($permit['PermitNumber'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Issuing Authority</label>
                <input type="text" name="IssuingAuthority" class="form-control" value="<?= sanitize($permit['IssuingAuthority'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Issue Date <span class="text-danger">*</span></label>
                <input type="date" name="IssueDate" class="form-control" value="<?= sanitize($permit['IssueDate'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Expiry Date <span class="text-danger">*</span></label>
                <input type="date" name="ExpiryDate" class="form-control" value="<?= sanitize($permit['ExpiryDate'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="Status" class="form-select" required>
                    <?php $st = $permit['Status'] ?? 'Active'; ?>
                    <option value="Active" <?= $st === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Expiring Soon" <?= $st === 'Expiring Soon' ? 'selected' : '' ?>>Expiring Soon</option>
                    <option value="Expired" <?= $st === 'Expired' ? 'selected' : '' ?>>Expired</option>
                    <option value="Suspended" <?= $st === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="Notes" class="form-control" rows="3"><?= sanitize($permit['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($permit) ? 'Update' : 'Create' ?> Permit</button>
                <a href="?route=permits" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
