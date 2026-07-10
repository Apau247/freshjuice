<?php $pageTitle = isset($_GET['id']) ? 'Edit Drill' : 'Schedule Drill'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-fire-extinguisher me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=safety/drill_index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Drills</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=safety/drill_save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="DrillID" class="form-label">Drill ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="DrillID" name="DrillID" value="<?php echo sanitize($drill['DrillID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="DrillDate" class="form-label">Drill Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="DrillDate" name="DrillDate" value="<?php echo sanitize($drill['DrillDate'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="DrillType" class="form-label">Drill Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="DrillType" name="DrillType" value="<?php echo sanitize($drill['DrillType'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="Location" class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Location" name="Location" value="<?php echo sanitize($drill['Location'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ParticipantsCount" class="form-label">Participants Count</label>
                        <input type="number" class="form-control" id="ParticipantsCount" name="ParticipantsCount" value="<?php echo sanitize($drill['ParticipantsCount'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="DurationMinutes" class="form-label">Duration (Minutes)</label>
                        <input type="number" class="form-control" id="DurationMinutes" name="DurationMinutes" value="<?php echo sanitize($drill['DurationMinutes'] ?? ''); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Outcome" class="form-label">Outcome</label>
                    <textarea class="form-control" id="Outcome" name="Outcome" rows="3"><?php echo sanitize($drill['Outcome'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="IssuesFound" class="form-label">Issues Found</label>
                    <textarea class="form-control" id="IssuesFound" name="IssuesFound" rows="3"><?php echo sanitize($drill['IssuesFound'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="CorrectiveAction" class="form-label">Corrective Action</label>
                    <textarea class="form-control" id="CorrectiveAction" name="CorrectiveAction" rows="3"><?php echo sanitize($drill['CorrectiveAction'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="Status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="Status" name="Status" required>
                        <option value="Scheduled" <?php echo (isset($drill['Status']) && $drill['Status'] === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                        <option value="Completed" <?php echo (isset($drill['Status']) && $drill['Status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo (isset($drill['Status']) && $drill['Status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Drill</button>
                <a href="?route=safety/drill_index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
