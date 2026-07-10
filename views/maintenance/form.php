<?php $pageTitle = isset($record) ? 'Edit Maintenance Record' : 'New Maintenance Record'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-wrench me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=maintenance" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($record) ? 'maintenance/edit&id=' . urlencode($record['MaintenanceID']) : 'maintenance/create' ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Machine <span class="text-danger">*</span></label>
                <select name="machine_id" class="form-select" required>
                    <option value="">Select Machine</option>
                    <?php if (isset($machines)): foreach ($machines as $m): ?>
                    <option value="<?= sanitize($m['MachineID']) ?>" <?= (isset($record) && ($record['MachineID'] ?? '') === $m['MachineID']) ? 'selected' : '' ?>><?= sanitize($m['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Maintenance Type <span class="text-danger">*</span></label>
                <select name="maintenance_type" class="form-select" required>
                    <option value="">Select Type</option>
                    <option value="Preventive" <?= (isset($record) && ($record['MaintenanceType'] ?? '') === 'Preventive') ? 'selected' : '' ?>>Preventive</option>
                    <option value="Corrective" <?= (isset($record) && ($record['MaintenanceType'] ?? '') === 'Corrective') ? 'selected' : '' ?>>Corrective</option>
                    <option value="Emergency" <?= (isset($record) && ($record['MaintenanceType'] ?? '') === 'Emergency') ? 'selected' : '' ?>>Emergency</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Maintenance Date <span class="text-danger">*</span></label>
                <input type="date" name="maintenance_date" class="form-control" value="<?= sanitize($record['MaintenanceDate'] ?? $record['maintenance_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Downtime (hours)</label>
                <input type="number" step="0.1" name="downtime" class="form-control" value="<?= sanitize((string)($record['Downtime'] ?? '0')) ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Cost ($) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="cost" class="form-control" value="<?= sanitize((string)($record['Cost'] ?? '0')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Next Service Date</label>
                <input type="date" name="next_service_date" class="form-control" value="<?= sanitize($record['NextServiceDate'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Technician</label>
                <select name="technician_id" class="form-select">
                    <option value="">Select Technician</option>
                    <?php if (isset($users)): foreach ($users as $u): ?>
                    <option value="<?= sanitize($u['UserID']) ?>" <?= (isset($record) && ($record['TechnicianID'] ?? '') === $u['UserID']) ? 'selected' : '' ?>><?= sanitize($u['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Scheduled" <?= (isset($record) && ($record['Status'] ?? '') === 'Scheduled') ? 'selected' : '' ?>>Scheduled</option>
                    <option value="In Progress" <?= (isset($record) && ($record['Status'] ?? '') === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= (isset($record) && ($record['Status'] ?? '') === 'Completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= (isset($record) && ($record['Status'] ?? '') === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="2"><?= sanitize($record['Description'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Spare Parts</label>
                <textarea name="spare_parts" class="form-control" rows="2"><?= sanitize($record['SpareParts'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($record) ? 'Update' : 'Create' ?> Record</button>
                <a href="?route=maintenance" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
