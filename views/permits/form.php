<?php $pageTitle = isset($_GET['id']) ? 'Edit Permit' : 'Add Permit'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-certificate me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=permits/index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Permits</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=permits/save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="PermitID" class="form-label">Permit ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="PermitID" name="PermitID" value="<?php echo sanitize($permit['PermitID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="PermitName" class="form-label">Permit Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="PermitName" name="PermitName" value="<?php echo sanitize($permit['PermitName'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="PermitType" class="form-label">Permit Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="PermitType" name="PermitType" value="<?php echo sanitize($permit['PermitType'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="IssuingAuthority" class="form-label">Issuing Authority</label>
                        <input type="text" class="form-control" id="IssuingAuthority" name="IssuingAuthority" value="<?php echo sanitize($permit['IssuingAuthority'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="PermitNumber" class="form-label">Permit Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="PermitNumber" name="PermitNumber" value="<?php echo sanitize($permit['PermitNumber'] ?? ''); ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="IssueDate" class="form-label">Issue Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="IssueDate" name="IssueDate" value="<?php echo sanitize($permit['IssueDate'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ExpiryDate" class="form-label">Expiry Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="ExpiryDate" name="ExpiryDate" value="<?php echo sanitize($permit['ExpiryDate'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="Status" name="Status" required>
                            <option value="Active" <?php echo (isset($permit['Status']) && $permit['Status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Expiring Soon" <?php echo (isset($permit['Status']) && $permit['Status'] === 'Expiring Soon') ? 'selected' : ''; ?>>Expiring Soon</option>
                            <option value="Expired" <?php echo (isset($permit['Status']) && $permit['Status'] === 'Expired') ? 'selected' : ''; ?>>Expired</option>
                            <option value="Suspended" <?php echo (isset($permit['Status']) && $permit['Status'] === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="Notes" name="Notes" rows="3"><?php echo sanitize($permit['Notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Permit</button>
                <a href="?route=permits/index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
