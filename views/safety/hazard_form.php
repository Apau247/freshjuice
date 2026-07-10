<?php $pageTitle = isset($_GET['id']) ? 'Edit Hazard' : 'Create Hazard'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-exclamation-triangle me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=safety/hazard_index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to Hazard Register</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=safety/hazard_save">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="HazardID" class="form-label">Hazard ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="HazardID" name="HazardID" value="<?php echo sanitize($hazard['HazardID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="RiskCategory" class="form-label">Risk Category <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="RiskCategory" name="RiskCategory" value="<?php echo sanitize($hazard['RiskCategory'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="HazardDescription" class="form-label">Hazard Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="HazardDescription" name="HazardDescription" rows="3" required><?php echo sanitize($hazard['HazardDescription'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="Likelihood" class="form-label">Likelihood <span class="text-danger">*</span></label>
                        <select class="form-select" id="Likelihood" name="Likelihood" required>
                            <option value="">Select Likelihood</option>
                            <option value="1" <?php echo (isset($hazard['Likelihood']) && $hazard['Likelihood'] == '1') ? 'selected' : ''; ?>>1 - Rare</option>
                            <option value="2" <?php echo (isset($hazard['Likelihood']) && $hazard['Likelihood'] == '2') ? 'selected' : ''; ?>>2 - Unlikely</option>
                            <option value="3" <?php echo (isset($hazard['Likelihood']) && $hazard['Likelihood'] == '3') ? 'selected' : ''; ?>>3 - Possible</option>
                            <option value="4" <?php echo (isset($hazard['Likelihood']) && $hazard['Likelihood'] == '4') ? 'selected' : ''; ?>>4 - Likely</option>
                            <option value="5" <?php echo (isset($hazard['Likelihood']) && $hazard['Likelihood'] == '5') ? 'selected' : ''; ?>>5 - Almost Certain</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Consequence" class="form-label">Consequence <span class="text-danger">*</span></label>
                        <select class="form-select" id="Consequence" name="Consequence" required>
                            <option value="">Select Consequence</option>
                            <option value="1" <?php echo (isset($hazard['Consequence']) && $hazard['Consequence'] == '1') ? 'selected' : ''; ?>>1 - Insignificant</option>
                            <option value="2" <?php echo (isset($hazard['Consequence']) && $hazard['Consequence'] == '2') ? 'selected' : ''; ?>>2 - Minor</option>
                            <option value="3" <?php echo (isset($hazard['Consequence']) && $hazard['Consequence'] == '3') ? 'selected' : ''; ?>>3 - Moderate</option>
                            <option value="4" <?php echo (isset($hazard['Consequence']) && $hazard['Consequence'] == '4') ? 'selected' : ''; ?>>4 - Major</option>
                            <option value="5" <?php echo (isset($hazard['Consequence']) && $hazard['Consequence'] == '5') ? 'selected' : ''; ?>>5 - Catastrophic</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Risk Rating</label>
                        <div class="form-control bg-light" id="riskRatingDisplay">--</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="ControlMeasures" class="form-label">Control Measures</label>
                    <textarea class="form-control" id="ControlMeasures" name="ControlMeasures" rows="3"><?php echo sanitize($hazard['ControlMeasures'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="ResponsiblePerson" class="form-label">Responsible Person</label>
                        <input type="text" class="form-control" id="ResponsiblePerson" name="ResponsiblePerson" value="<?php echo sanitize($hazard['ResponsiblePerson'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ReviewDate" class="form-label">Review Date</label>
                        <input type="date" class="form-control" id="ReviewDate" name="ReviewDate" value="<?php echo sanitize($hazard['ReviewDate'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="Status" name="Status" required>
                            <option value="Active" <?php echo (isset($hazard['Status']) && $hazard['Status'] === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Mitigated" <?php echo (isset($hazard['Status']) && $hazard['Status'] === 'Mitigated') ? 'selected' : ''; ?>>Mitigated</option>
                            <option value="Closed" <?php echo (isset($hazard['Status']) && $hazard['Status'] === 'Closed') ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Hazard</button>
                <a href="?route=safety/hazard_index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('Likelihood').addEventListener('change', calculateRisk);
document.getElementById('Consequence').addEventListener('change', calculateRisk);

function calculateRisk() {
    var l = parseInt(document.getElementById('Likelihood').value) || 0;
    var c = parseInt(document.getElementById('Consequence').value) || 0;
    var rating = l * c;
    var display = document.getElementById('riskRatingDisplay');
    if (l > 0 && c > 0) {
        var label = 'Low';
        var cls = 'text-success';
        if (rating >= 15) { label = 'Extreme'; cls = 'text-danger'; }
        else if (rating >= 8) { label = 'High'; cls = 'text-warning'; }
        else if (rating >= 4) { label = 'Medium'; cls = 'text-info'; }
        display.innerHTML = '<span class="' + cls + ' fw-bold">' + rating + ' - ' + label + '</span>';
    } else {
        display.innerHTML = '--';
    }
}
</script>
