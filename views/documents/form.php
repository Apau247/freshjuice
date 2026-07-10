<?php $pageTitle = isset($_GET['id']) ? 'Edit Document' : 'Add Document'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-file-alt me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=documents/index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Documents</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=documents/save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="DocID" class="form-label">Document ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="DocID" name="DocID" value="<?php echo sanitize($document['DocID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="Title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Title" name="Title" value="<?php echo sanitize($document['Title'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="DocType" class="form-label">Document Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="DocType" name="DocType" value="<?php echo sanitize($document['DocType'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Version" class="form-label">Version <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Version" name="Version" value="<?php echo sanitize($document['Version'] ?? '1.0'); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="Department" name="Department" value="<?php echo sanitize($document['Department'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="FilePath" class="form-label">File Path</label>
                    <input type="text" class="form-control" id="FilePath" name="FilePath" value="<?php echo sanitize($document['FilePath'] ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="Description" class="form-label">Description</label>
                    <textarea class="form-control" id="Description" name="Description" rows="3"><?php echo sanitize($document['Description'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="EffectiveDate" class="form-label">Effective Date</label>
                        <input type="date" class="form-control" id="EffectiveDate" name="EffectiveDate" value="<?php echo sanitize($document['EffectiveDate'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ReviewDate" class="form-label">Review Date</label>
                        <input type="date" class="form-control" id="ReviewDate" name="ReviewDate" value="<?php echo sanitize($document['ReviewDate'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="Status" name="Status" required>
                            <option value="Draft" <?php echo (isset($document['Status']) && $document['Status'] === 'Draft') ? 'selected' : ''; ?>>Draft</option>
                            <option value="Under Review" <?php echo (isset($document['Status']) && $document['Status'] === 'Under Review') ? 'selected' : ''; ?>>Under Review</option>
                            <option value="Approved" <?php echo (isset($document['Status']) && $document['Status'] === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                            <option value="Obsolete" <?php echo (isset($document['Status']) && $document['Status'] === 'Obsolete') ? 'selected' : ''; ?>>Obsolete</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Document</button>
                <a href="?route=documents/index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
