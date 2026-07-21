<?php $pageTitle = isset($cert) ? 'Edit Certification' : 'New Certification'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-award me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=certifications" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($cert) ? 'certifications/edit&id=' . urlencode($cert['CertID']) : 'certifications/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Certification Name <span class="text-danger">*</span></label>
                <input type="text" name="cert_name" class="form-control" value="<?= sanitize($cert['CertName'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Certification Type <span class="text-danger">*</span></label>
                <select name="cert_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Food Safety" <?= (isset($cert) && ($cert['CertType'] ?? '') === 'Food Safety') ? 'selected' : '' ?>>Food Safety</option>
                    <option value="Quality Management" <?= (isset($cert) && ($cert['CertType'] ?? '') === 'Quality Management') ? 'selected' : '' ?>>Quality Management</option>
                    <option value="Environmental" <?= (isset($cert) && ($cert['CertType'] ?? '') === 'Environmental') ? 'selected' : '' ?>>Environmental</option>
                    <option value="Organic" <?= (isset($cert) && ($cert['CertType'] ?? '') === 'Organic') ? 'selected' : '' ?>>Organic</option>
                    <option value="Halal" <?= (isset($cert) && ($cert['CertType'] ?? '') === 'Halal') ? 'selected' : '' ?>>Halal</option>
                    <option value="Health & Safety" <?= (isset($cert) && ($cert['CertType'] ?? '') === 'Health & Safety') ? 'selected' : '' ?>>Health & Safety</option>
                    <option value="Other" <?= (isset($cert) && ($cert['CertType'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Issuing Authority</label>
                <input type="text" name="issuing_authority" class="form-control" value="<?= sanitize($cert['IssuingAuthority'] ?? $cert['issuing_authority'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Issue Date <span class="text-danger">*</span></label>
                <input type="date" name="issue_date" class="form-control" value="<?= sanitize($cert['IssueDate'] ?? $cert['issue_date'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Expiry Date <span class="text-danger">*</span></label>
                <input type="date" name="expiry_date" class="form-control" value="<?= sanitize($cert['ExpiryDate'] ?? $cert['expiry_date'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= (isset($cert) && ($cert['Status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="Expired" <?= (isset($cert) && ($cert['Status'] ?? '') === 'Expired') ? 'selected' : '' ?>>Expired</option>
                    <option value="Pending Renewal" <?= (isset($cert) && ($cert['Status'] ?? '') === 'Pending Renewal') ? 'selected' : '' ?>>Pending Renewal</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Document Path</label>
                <input type="text" name="document_path" class="form-control" value="<?= sanitize($cert['DocumentPath'] ?? '') ?>">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= sanitize($cert['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($cert) ? 'Update' : 'Create' ?> Certification</button>
                <a href="?route=certifications" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
