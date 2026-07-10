<?php $pageTitle = isset($log) ? 'Edit Generator Log' : 'New Generator Log'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-fuel-pump me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=power" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($log) ? 'power/generator/edit&id=' . urlencode($log['LogID']) : 'power/generator/create' ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="<?= sanitize($log['Date'] ?? $log['date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Start Time</label>
                <input type="time" name="start_time" class="form-control" value="<?= sanitize($log['StartTime'] ?? $log['start_time'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">End Time</label>
                <input type="time" name="end_time" class="form-control" value="<?= sanitize($log['EndTime'] ?? $log['end_time'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Runtime (hours)</label>
                <input type="number" step="0.1" name="runtime_hrs" class="form-control" value="<?= sanitize((string)($log['RuntimeHrs'] ?? '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Fuel Used</label>
                <input type="number" step="0.01" name="fuel_used" class="form-control" value="<?= sanitize((string)($log['FuelUsed'] ?? '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Fuel Unit</label>
                <select name="fuel_unit" class="form-select">
                    <option value="litres" <?= (isset($log) && ($log['FuelUnit'] ?? '') === 'litres') ? 'selected' : '' ?>>litres</option>
                    <option value="gallons" <?= (isset($log) && ($log['FuelUnit'] ?? '') === 'gallons') ? 'selected' : '' ?>>gallons</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Reason</label>
                <select name="reason" class="form-select">
                    <option value="">Select Reason</option>
                    <option value="Power Outage" <?= (isset($log) && ($log['Reason'] ?? '') === 'Power Outage') ? 'selected' : '' ?>>Power Outage</option>
                    <option value="Load Shedding" <?= (isset($log) && ($log['Reason'] ?? '') === 'Load Shedding') ? 'selected' : '' ?>>Load Shedding</option>
                    <option value="Grid Failure" <?= (isset($log) && ($log['Reason'] ?? '') === 'Grid Failure') ? 'selected' : '' ?>>Grid Failure</option>
                    <option value="Testing" <?= (isset($log) && ($log['Reason'] ?? '') === 'Testing') ? 'selected' : '' ?>>Testing</option>
                    <option value="Scheduled" <?= (isset($log) && ($log['Reason'] ?? '') === 'Scheduled') ? 'selected' : '' ?>>Scheduled</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Notes</label>
                <input type="text" name="notes" class="form-control" value="<?= sanitize($log['Notes'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($log) ? 'Update' : 'Save' ?> Log</button>
                <a href="?route=power" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
