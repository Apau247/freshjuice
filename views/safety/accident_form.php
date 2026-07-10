<?php $pageTitle = isset($_GET['id']) ? 'Edit Incident Report' : 'Report Incident'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-ambulance me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=safety/accident_index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Reports</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=safety/accident_save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="AccidentID" class="form-label">Accident ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="AccidentID" name="AccidentID" value="<?php echo sanitize($accident['AccidentID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="IncidentDate" class="form-label">Incident Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="IncidentDate" name="IncidentDate" value="<?php echo sanitize($accident['IncidentDate'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="Location" class="form-label">Location <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="Location" name="Location" value="<?php echo sanitize($accident['Location'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="IncidentType" class="form-label">Incident Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="IncidentType" name="IncidentType" value="<?php echo sanitize($accident['IncidentType'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="Description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="Description" name="Description" rows="4" required><?php echo sanitize($accident['Description'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="Injuries" class="form-label">Injuries</label>
                    <textarea class="form-control" id="Injuries" name="Injuries" rows="2"><?php echo sanitize($accident['Injuries'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="RootCause" class="form-label">Root Cause</label>
                    <textarea class="form-control" id="RootCause" name="RootCause" rows="3"><?php echo sanitize($accident['RootCause'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="CorrectiveAction" class="form-label">Corrective Action</label>
                    <textarea class="form-control" id="CorrectiveAction" name="CorrectiveAction" rows="3"><?php echo sanitize($accident['CorrectiveAction'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="Status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-select" id="Status" name="Status" required>
                        <option value="Open" <?php echo (isset($accident['Status']) && $accident['Status'] === 'Open') ? 'selected' : ''; ?>>Open</option>
                        <option value="Under Investigation" <?php echo (isset($accident['Status']) && $accident['Status'] === 'Under Investigation') ? 'selected' : ''; ?>>Under Investigation</option>
                        <option value="Closed" <?php echo (isset($accident['Status']) && $accident['Status'] === 'Closed') ? 'selected' : ''; ?>>Closed</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Report</button>
                <a href="?route=safety/accident_index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
