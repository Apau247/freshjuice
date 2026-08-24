<?php $pageTitle = isset($batch) ? 'Edit Production Batch' : 'New Production Batch'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=production" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($batch) ? 'production/edit&id=' . urlencode($batch['BatchID']) : 'production/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-12">
                <h6 class="fw-bold text-uppercase text-muted mb-1" style="font-size:0.72rem;letter-spacing:.06em;">
                    <i class="bi bi-box-seam me-1"></i> Batch Details
                </h6>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Batch Number <span class="text-danger">*</span></label>
                <input type="text" name="batch_number" class="form-control" value="<?= sanitize($batch['BatchNumber'] ?? $suggestedBatchNumber ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Production Date <span class="text-danger">*</span></label>
                <input type="date" name="production_date" class="form-control" value="<?= sanitize($batch['ProductionDate'] ?? $batch['production_date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Flavour <span class="text-danger">*</span></label>
                <select name="flavour" class="form-select" required>
                    <?= trending_value_options(
                        ['Orange', 'Apple', 'Mango', 'Grape', 'Mixed Fruit', 'Lemon', 'Pineapple'],
                        $trends['flavour'] ?? null,
                        isset($batch) ? ($batch['Flavour'] ?? null) : null,
                        'Select Flavour'
                    ) ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity <span class="text-danger">*</span></label>
                <input type="number" min="0" step="0.01" name="quantity" class="form-control" value="<?= sanitize((string)($batch['Quantity'] ?? '')) ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold">Unit</label>
                <select name="unit" class="form-select">
                    <?= trending_value_options(['litres', 'kg'], $trends['unit'] ?? null, isset($batch) ? ($batch['Unit'] ?? null) : null) ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Pending" <?= (isset($batch) && ($batch['Status'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= (isset($batch) && ($batch['Status'] ?? '') === 'In Progress') ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= (isset($batch) && ($batch['Status'] ?? '') === 'Completed') ? 'selected' : '' ?>>Completed</option>
                    <option value="Rejected" <?= (isset($batch) && ($batch['Status'] ?? '') === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                    <option value="Cancelled" <?= (isset($batch) && ($batch['Status'] ?? '') === 'Cancelled') ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Operator / User</label>
                <select name="user_id" class="form-select">
                    <option value="">Select Operator</option>
                    <?php if (isset($users)): ?>
                        <?= trending_options($users, 'UserID', 'Name', $trends['operator'] ?? null, isset($batch) ? ($batch['UserID'] ?? null) : null) ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-12">
                <hr class="mt-2 mb-3">
                <h6 class="fw-bold text-uppercase text-muted" style="font-size:0.72rem;letter-spacing:.06em;">
                    <i class="bi bi-basket3 me-1"></i> Materials Consumed
                </h6>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Raw Material</label>
                <?php $rawTrend = $trends['raw'] ?? null; ?>
                <select name="raw_material_id" class="form-select"
                        data-unit-target="unit"
                        data-lookup='<?= sanitize(json_encode(array_combine(
                            array_column($rawMaterials, 'MaterialID'),
                            array_map(fn($r) => ['unit' => $r['Unit'] ?? '', 'name' => $r['Name'] ?? '', 'stock' => $r['CurrentStock'] ?? 0], $rawMaterials)
                        ), JSON_HEX_APOS | JSON_HEX_QUOT)) ?>'>
                    <?= trending_options($rawMaterials, 'MaterialID', 'Name', $rawTrend, isset($batch) ? ($batch['RawMaterialID'] ?? null) : null, 'Select RM') ?>
                </select>
                <div class="form-text" data-cascade-note></div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Packaging Material</label>
                <select name="packaging_material_id" class="form-select">
                    <?= trending_options($packagingMaterials, 'PackageID', 'Name', $trends['pkg'] ?? null, isset($batch) ? ($batch['PackagingMaterialID'] ?? null) : null, 'Select PKG') ?>
                </select>
            </div>

            <div class="col-12">
                <hr class="mt-2 mb-3">
                <h6 class="fw-bold text-uppercase text-muted" style="font-size:0.72rem;letter-spacing:.06em;">
                    <i class="bi bi-cpu me-1"></i> Equipment Used
                    <span class="text-muted fw-normal text-lowercase" style="font-size:0.7rem;">(machinery only &mdash; tracked separately from materials)</span>
                </h6>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Machine / Equipment</label>
                <?php
                // Equipment is not "stuff": each machine option carries its type
                // and live status so the form can warn before an unusable
                // machine is picked (the server rejects it as a final gate).
                $machineRows = [];
                $machineAttrs = [];
                foreach ($machines as $m) {
                    $status = (string)($m['Status'] ?? '');
                    $type = (string)($m['Type'] ?? '');
                    $m['display'] = $m['Name'] . ($type !== '' ? ' (' . $type . ')' : '')
                        . (!in_array($status, ['Operational', ''], true) ? ' -- ' . strtoupper($status) : '');
                    $machineAttrs[(string)$m['MachineID']] = 'data-status="' . sanitize($status) . '" data-mname="' . sanitize((string)$m['Name']) . '"';
                    $machineRows[] = $m;
                }
                ?>
                <select name="machine_id" class="form-select" data-equipment>
                    <?= trending_options($machineRows, 'MachineID', 'display', $trends['machine'] ?? null, isset($batch) ? ($batch['MachineID'] ?? null) : null, 'Select Machine', $machineAttrs) ?>
                </select>
                <div class="form-text" data-machine-note></div>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= sanitize($batch['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($batch) ? 'Update' : 'Create' ?> Batch</button>
                <a href="?route=production" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
