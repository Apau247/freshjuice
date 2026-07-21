<?php $pageTitle = isset($document) ? 'Edit Document' : 'New Document'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-folder2-open me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=documents" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($document) ? 'documents/edit&id=' . urlencode($document['DocID']) : 'documents/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= sanitize($document['Title'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Document Type <span class="text-danger">*</span></label>
                <input type="text" name="doc_type" class="form-control" value="<?= sanitize($document['DocType'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Version</label>
                <input type="text" name="version" class="form-control" value="<?= sanitize($document['Version'] ?? '1.0') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Department</label>
                <input type="text" name="department" class="form-control" value="<?= sanitize($document['Department'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">File Path</label>
                <input type="text" name="file_path" class="form-control" value="<?= sanitize($document['FilePath'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <?php $st = $document['Status'] ?? 'Draft'; ?>
                    <option value="Draft" <?= $st === 'Draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="Under Review" <?= $st === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                    <option value="Approved" <?= $st === 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Obsolete" <?= $st === 'Obsolete' ? 'selected' : '' ?>>Obsolete</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= sanitize($document['Description'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Effective Date</label>
                <input type="date" name="effective_date" class="form-control" value="<?= sanitize($document['EffectiveDate'] ?? '') ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Review Date</label>
                <input type="date" name="review_date" class="form-control" value="<?= sanitize($document['ReviewDate'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($document) ? 'Update' : 'Create' ?> Document</button>
                <a href="?route=documents" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
