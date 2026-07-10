<?php $pageTitle = isset($_GET['id']) ? 'Edit Training' : 'Add Training'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-graduation-cap me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=training/index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Training Records</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=training/save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="TrainingID" class="form-label">Training ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TrainingID" name="TrainingID" value="<?php echo sanitize($training['TrainingID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="StaffID" class="form-label">Staff <span class="text-danger">*</span></label>
                        <select class="form-select" id="StaffID" name="StaffID" required>
                            <option value="">Select Staff Member</option>
                            <?php if (!empty($staffList)): ?>
                                <?php foreach ($staffList as $staff): ?>
                                    <option value="<?php echo sanitize($staff['StaffID']); ?>" <?php echo (isset($training['StaffID']) && $training['StaffID'] == $staff['StaffID']) ? 'selected' : ''; ?>><?php echo sanitize($staff['FullName']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="TrainingType" class="form-label">Training Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TrainingType" name="TrainingType" value="<?php echo sanitize($training['TrainingType'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="TrainingDate" class="form-label">Training Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="TrainingDate" name="TrainingDate" value="<?php echo sanitize($training['TrainingDate'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Duration" class="form-label">Duration</label>
                        <input type="text" class="form-control" id="Duration" name="Duration" value="<?php echo sanitize($training['Duration'] ?? ''); ?>" placeholder="e.g. 2 hours">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="Trainer" class="form-label">Trainer</label>
                        <input type="text" class="form-control" id="Trainer" name="Trainer" value="<?php echo sanitize($training['Trainer'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="CertificateNo" class="form-label">Certificate No.</label>
                        <input type="text" class="form-control" id="CertificateNo" name="CertificateNo" value="<?php echo sanitize($training['CertificateNo'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ExpiryDate" class="form-label">Cert Expiry Date</label>
                        <input type="date" class="form-control" id="ExpiryDate" name="ExpiryDate" value="<?php echo sanitize($training['ExpiryDate'] ?? ''); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="Status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="Status" name="Status" required>
                            <option value="Scheduled" <?php echo (isset($training['Status']) && $training['Status'] === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="In Progress" <?php echo (isset($training['Status']) && $training['Status'] === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Completed" <?php echo (isset($training['Status']) && $training['Status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                            <option value="Cancelled" <?php echo (isset($training['Status']) && $training['Status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="Notes" name="Notes" rows="3"><?php echo sanitize($training['Notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Training</button>
                <a href="?route=training/index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
