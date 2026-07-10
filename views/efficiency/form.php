<?php $pageTitle = isset($_GET['id']) ? 'Edit Efficiency Data' : 'Record Efficiency Data'; ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-chart-line me-2"></i><?php echo $pageTitle; ?></h4>
        <a href="?route=efficiency/index" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back to OEE Dashboard</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="?route=efficiency/save">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="EfficiencyID" class="form-label">Efficiency ID <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="EfficiencyID" name="EfficiencyID" value="<?php echo sanitize($efficiency['EfficiencyID'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="RecordDate" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="RecordDate" name="RecordDate" value="<?php echo sanitize($efficiency['RecordDate'] ?? ''); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="Shift" class="form-label">Shift <span class="text-danger">*</span></label>
                        <select class="form-select" id="Shift" name="Shift" required>
                            <option value="">Select Shift</option>
                            <option value="Day" <?php echo (isset($efficiency['Shift']) && $efficiency['Shift'] === 'Day') ? 'selected' : ''; ?>>Day</option>
                            <option value="Night" <?php echo (isset($efficiency['Shift']) && $efficiency['Shift'] === 'Night') ? 'selected' : ''; ?>>Night</option>
                            <option value="Weekend" <?php echo (isset($efficiency['Shift']) && $efficiency['Shift'] === 'Weekend') ? 'selected' : ''; ?>>Weekend</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="MachineID" class="form-label">Machine <span class="text-danger">*</span></label>
                    <select class="form-select" id="MachineID" name="MachineID" required>
                        <option value="">Select Machine</option>
                        <?php if (!empty($machines)): ?>
                            <?php foreach ($machines as $machine): ?>
                                <option value="<?php echo sanitize($machine['MachineID']); ?>" <?php echo (isset($efficiency['MachineID']) && $efficiency['MachineID'] == $machine['MachineID']) ? 'selected' : ''; ?>><?php echo sanitize($machine['MachineName']); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="PlannedRunTime" class="form-label">Planned Run Time (min) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="PlannedRunTime" name="PlannedRunTime" value="<?php echo sanitize($efficiency['PlannedRunTime'] ?? ''); ?>" required oninput="calculateOEE()">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="ActualRunTime" class="form-label">Actual Run Time (min) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="ActualRunTime" name="ActualRunTime" value="<?php echo sanitize($efficiency['ActualRunTime'] ?? ''); ?>" required oninput="calculateOEE()">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="DowntimeMinutes" class="form-label">Downtime (min)</label>
                        <input type="number" class="form-control" id="DowntimeMinutes" name="DowntimeMinutes" value="<?php echo sanitize($efficiency['DowntimeMinutes'] ?? '0'); ?>" oninput="calculateOEE()">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="TotalProduced" class="form-label">Total Produced <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="TotalProduced" name="TotalProduced" value="<?php echo sanitize($efficiency['TotalProduced'] ?? ''); ?>" required oninput="calculateOEE()">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="GoodProduced" class="form-label">Good Produced <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="GoodProduced" name="GoodProduced" value="<?php echo sanitize($efficiency['GoodProduced'] ?? ''); ?>" required oninput="calculateOEE()">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="DefectCount" class="form-label">Defect Count</label>
                        <input type="number" class="form-control" id="DefectCount" name="DefectCount" value="<?php echo sanitize($efficiency['DefectCount'] ?? '0'); ?>">
                    </div>
                </div>

                <div class="card bg-light mb-3">
                    <div class="card-body">
                        <h6>Calculated OEE</h6>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <small class="text-muted">Availability</small>
                                <h5 id="availabilityDisplay">0%</h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Performance</small>
                                <h5 id="performanceDisplay">0%</h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">Quality</small>
                                <h5 id="qualityDisplay">0%</h5>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted">OEE</small>
                                <h4 id="oeeDisplay" class="text-primary">0%</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Record</button>
                <a href="?route=efficiency/index" class="btn btn-secondary ms-2">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
function calculateOEE() {
    var planned = parseFloat(document.getElementById('PlannedRunTime').value) || 0;
    var actual = parseFloat(document.getElementById('ActualRunTime').value) || 0;
    var total = parseFloat(document.getElementById('TotalProduced').value) || 0;
    var good = parseFloat(document.getElementById('GoodProduced').value) || 0;

    var availability = planned > 0 ? (actual / planned * 100) : 0;
    var idealCycleRate = 1;
    var performance = actual > 0 ? (total / actual * idealCycleRate * 100) : 0;
    if (performance > 100) performance = 100;
    var quality = total > 0 ? (good / total * 100) : 0;
    var oee = (availability * performance * quality) / 10000;

    document.getElementById('availabilityDisplay').textContent = availability.toFixed(1) + '%';
    document.getElementById('performanceDisplay').textContent = performance.toFixed(1) + '%';
    document.getElementById('qualityDisplay').textContent = quality.toFixed(1) + '%';
    document.getElementById('oeeDisplay').textContent = oee.toFixed(1) + '%';
}

calculateOEE();
</script>
