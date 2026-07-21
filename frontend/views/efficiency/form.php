<?php $pageTitle = isset($efficiency) ? 'Edit Efficiency Data' : 'Record Efficiency Data'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=efficiency" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($efficiency) ? 'efficiency/edit&id=' . urlencode($efficiency['EfficiencyID']) : 'efficiency/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="record_date" class="form-control" value="<?= sanitize($efficiency['Date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Shift</label>
                <select name="shift" class="form-select">
                    <option value="">Select Shift</option>
                    <?php $sh = $efficiency['Shift'] ?? ''; ?>
                    <option value="Morning" <?= $sh === 'Morning' ? 'selected' : '' ?>>Morning</option>
                    <option value="Afternoon" <?= $sh === 'Afternoon' ? 'selected' : '' ?>>Afternoon</option>
                    <option value="Night" <?= $sh === 'Night' ? 'selected' : '' ?>>Night</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Machine <span class="text-danger">*</span></label>
                <select name="machine_id" class="form-select" required>
                    <option value="">Select Machine</option>
                    <?php if (!empty($machines)): foreach ($machines as $m): ?>
                    <option value="<?= sanitize($m['MachineID']) ?>" <?= (isset($efficiency) && ($efficiency['MachineID'] ?? '') === $m['MachineID']) ? 'selected' : '' ?>><?= sanitize($m['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Planned Run Time (min) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="planned_run_time" class="form-control" value="<?= sanitize((string)($efficiency['PlannedRunTime'] ?? '')) ?>" required oninput="calcOEE()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Actual Run Time (min) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="actual_run_time" class="form-control" value="<?= sanitize((string)($efficiency['ActualRunTime'] ?? '')) ?>" required oninput="calcOEE()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Downtime (min)</label>
                <input type="number" step="0.01" name="downtime_minutes" class="form-control" value="<?= sanitize((string)($efficiency['DowntimeMinutes'] ?? '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Total Produced <span class="text-danger">*</span></label>
                <input type="number" name="total_produced" class="form-control" value="<?= sanitize((string)($efficiency['TotalProduced'] ?? '')) ?>" required oninput="calcOEE()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Good Produced <span class="text-danger">*</span></label>
                <input type="number" name="good_produced" class="form-control" value="<?= sanitize((string)($efficiency['GoodProduced'] ?? '')) ?>" required oninput="calcOEE()">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Notes</label>
                <input type="text" name="notes" class="form-control" value="<?= sanitize($efficiency['Notes'] ?? '') ?>">
            </div>
            <div class="col-12">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="fw-semibold">Calculated OEE</h6>
                        <div class="row text-center">
                            <div class="col-md-3"><small class="text-muted">Availability</small><h5 id="availDisplay">0%</h5></div>
                            <div class="col-md-3"><small class="text-muted">Performance</small><h5 id="perfDisplay">0%</h5></div>
                            <div class="col-md-3"><small class="text-muted">Quality</small><h5 id="qualDisplay">0%</h5></div>
                            <div class="col-md-3"><small class="text-muted">OEE</small><h4 id="oeeDisplay" class="text-primary">0%</h4></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($efficiency) ? 'Update' : 'Create' ?> Record</button>
                <a href="?route=efficiency" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
function calcOEE() {
    var p = parseFloat(document.querySelector('[name=planned_run_time]').value) || 0;
    var a = parseFloat(document.querySelector('[name=actual_run_time]').value) || 0;
    var t = parseFloat(document.querySelector('[name=total_produced]').value) || 0;
    var g = parseFloat(document.querySelector('[name=good_produced]').value) || 0;
    var avail = p > 0 ? (a / p * 100) : 0;
    var perf = t > 0 && a > 0 ? (g / t * 100) : 0;
    var qual = t > 0 ? (g / t * 100) : 0;
    var oee = (avail * perf * qual) / 10000;
    document.getElementById('availDisplay').textContent = avail.toFixed(1) + '%';
    document.getElementById('perfDisplay').textContent = perf.toFixed(1) + '%';
    document.getElementById('qualDisplay').textContent = qual.toFixed(1) + '%';
    document.getElementById('oeeDisplay').textContent = oee.toFixed(1) + '%';
}
calcOEE();
</script>
