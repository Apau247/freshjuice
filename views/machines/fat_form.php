<?php $pageTitle = isset($_GET['id']) ? 'Edit FAT Record' : 'New FAT Record'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-flask me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=machines/fat_index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to FAT</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=machines/fat_save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="FAT_ID" class="form-label">FAT ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="FAT_ID" name="FAT_ID" value="<?php echo sanitize($fat['FAT_ID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="MachineID" class="form-label">Machine <span class="text-danger">*</span></label>
                        <select class="form-select" id="MachineID" name="MachineID" required>
                            <option value="">Select Machine</option>
                            <?php if (!empty($machines)): ?>
                                <?php foreach ($machines as $machine): ?>
                                    <option value="<?php echo sanitize($machine['MachineID']); ?>" <?php echo (isset($fat['MachineID']) && $fat['MachineID'] == $machine['MachineID']) ? 'selected' : ''; ?>><?php echo sanitize($machine['MachineName']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="TestDate" class="form-label">Test Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="TestDate" name="TestDate" value="<?php echo sanitize($fat['TestDate'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="TestType" class="form-label">Test Type <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="TestType" name="TestType" value="<?php echo sanitize($fat['TestType'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="ExpectedResult" class="form-label">Expected Result</label>
                    <textarea class="form-control" id="ExpectedResult" name="ExpectedResult" rows="3"><?php echo sanitize($fat['ExpectedResult'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="ActualResult" class="form-label">Actual Result</label>
                    <textarea class="form-control" id="ActualResult" name="ActualResult" rows="3"><?php echo sanitize($fat['ActualResult'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="Result" class="form-label">Result <span class="text-danger">*</span></label>
                        <select class="form-select" id="Result" name="Result" required>
                            <option value="">Select Result</option>
                            <option value="Pass" <?php echo (isset($fat['Result']) && $fat['Result'] === 'Pass') ? 'selected' : ''; ?>>Pass</option>
                            <option value="Fail" <?php echo (isset($fat['Result']) && $fat['Result'] === 'Fail') ? 'selected' : ''; ?>>Fail</option>
                            <option value="Conditional" <?php echo (isset($fat['Result']) && $fat['Result'] === 'Conditional') ? 'selected' : ''; ?>>Conditional Pass</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="Status" class="form-label">Status</label>
                        <select class="form-select" id="Status" name="Status">
                            <option value="Scheduled" <?php echo (isset($fat['Status']) && $fat['Status'] === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                            <option value="In Progress" <?php echo (isset($fat['Status']) && $fat['Status'] === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Completed" <?php echo (isset($fat['Status']) && $fat['Status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="DefectsFound" class="form-label">Defects Found</label>
                    <textarea class="form-control" id="DefectsFound" name="DefectsFound" rows="3"><?php echo sanitize($fat['DefectsFound'] ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="Notes" class="form-label">Notes</label>
                    <textarea class="form-control" id="Notes" name="Notes" rows="3"><?php echo sanitize($fat['Notes'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save FAT Record</button>
                <a href="?route=machines/fat_index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
