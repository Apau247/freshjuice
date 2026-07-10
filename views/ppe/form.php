<?php $pageTitle = isset($_GET['id']) ? 'Edit PPE' : 'Issue PPE'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-hard-hat me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=ppe/index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to PPE Tracking</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=ppe/save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="PPE_ID" class="form-label">PPE ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="PPE_ID" name="PPE_ID" value="<?php echo sanitize($ppe['PPE_ID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="StaffID" class="form-label">Staff <span class="text-danger">*</span></label>
                        <select class="form-select" id="StaffID" name="StaffID" required>
                            <option value="">Select Staff Member</option>
                            <?php if (!empty($staffList)): ?>
                                <?php foreach ($staffList as $staff): ?>
                                    <option value="<?php echo sanitize($staff['StaffID']); ?>" <?php echo (isset($ppe['StaffID']) && $ppe['StaffID'] == $staff['StaffID']) ? 'selected' : ''; ?>><?php echo sanitize($staff['FullName']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="PPESource" class="form-label">Equipment Type <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="PPESource" name="PPESource" value="<?php echo sanitize($ppe['PPESource'] ?? ''); ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="DateIssued" class="form-label">Date Issued <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="DateIssued" name="DateIssued" value="<?php echo sanitize($ppe['DateIssued'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ExpiryDate" class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" id="ExpiryDate" name="ExpiryDate" value="<?php echo sanitize($ppe['ExpiryDate'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Condition" class="form-label">Condition <span class="text-danger">*</span></label>
                        <select class="form-select" id="Condition" name="Condition" required>
                            <option value="">Select Condition</option>
                            <option value="New" <?php echo (isset($ppe['Condition']) && $ppe['Condition'] === 'New') ? 'selected' : ''; ?>>New</option>
                            <option value="Good" <?php echo (isset($ppe['Condition']) && $ppe['Condition'] === 'Good') ? 'selected' : ''; ?>>Good</option>
                            <option value="Fair" <?php echo (isset($ppe['Condition']) && $ppe['Condition'] === 'Fair') ? 'selected' : ''; ?>>Fair</option>
                            <option value="Poor" <?php echo (isset($ppe['Condition']) && $ppe['Condition'] === 'Poor') ? 'selected' : ''; ?>>Poor</option>
                            <option value="Damaged" <?php echo (isset($ppe['Condition']) && $ppe['Condition'] === 'Damaged') ? 'selected' : ''; ?>>Damaged</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="ReplacementNeeded" name="ReplacementNeeded" value="1" <?php echo (!empty($ppe['ReplacementNeeded'])) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="ReplacementNeeded">Replacement Needed</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="Notes" name="Notes" rows="3"><?php echo sanitize($ppe['Notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save PPE Record</button>
                <a href="?route=ppe/index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
