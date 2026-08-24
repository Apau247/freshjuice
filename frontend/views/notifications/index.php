<?php $pageTitle = 'Notifications'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-bell me-2"></i><?= $pageTitle ?></h5>
</div>

<?php
$lowStock = $notifications['low_stock'] ?? [];
$lowPackaging = $notifications['low_packaging'] ?? [];
$expiringCerts = $notifications['expiring_certs'] ?? [];
$expiringPermits = $notifications['expiring_permits'] ?? [];
$dueMaintenance = $notifications['due_maintenance'] ?? [];
$total = count($lowStock) + count($lowPackaging) + count($expiringCerts) + count($expiringPermits) + count($dueMaintenance);
?>

<?php if ($total === 0): ?>
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-check-circle text-success" style="font-size:3rem;"></i>
        <h5 class="mt-3 text-muted">All clear!</h5>
        <p class="text-muted">No notifications at this time.</p>
    </div>
</div>
<?php endif; ?>

<?php if (count($lowStock) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-danger">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Low Stock Alerts
            <span class="badge bg-danger ms-2"><?= count($lowStock) ?></span>
        </h6>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Material</th><th>Current Stock</th><th>Min Stock</th><th>Unit</th></tr>
            </thead>
            <tbody>
                <?php foreach ($lowStock as $item): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($item['Name'] ?? '') ?></td>
                    <td><span class="badge bg-danger"><?= sanitize($item['CurrentStock'] ?? 0) ?></span></td>
                    <td><?= sanitize($item['MinStock'] ?? 0) ?></td>
                    <td><?= sanitize($item['Unit'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (count($lowPackaging) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-danger">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-box text-danger me-2"></i>Packaging Reorder Alerts
            <span class="badge bg-danger ms-2"><?= count($lowPackaging) ?></span>
        </h6>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Material</th><th>Current Stock</th><th>Min Stock</th><th>Unit</th></tr>
            </thead>
            <tbody>
                <?php foreach ($lowPackaging as $item): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($item['Name'] ?? '') ?></td>
                    <td><span class="badge bg-danger"><?= sanitize($item['CurrentStock'] ?? 0) ?></span></td>
                    <td><?= sanitize($item['MinStock'] ?? 0) ?></td>
                    <td><?= sanitize($item['Unit'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (count($expiringCerts) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-warning">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-award text-warning me-2"></i>Expiring Certifications
            <span class="badge bg-warning text-dark ms-2"><?= count($expiringCerts) ?></span>
        </h6>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Certification</th><th>Expiry Date</th><th>Days Left</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($expiringCerts as $cert): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($cert['CertName'] ?? $cert['Name'] ?? '') ?></td>
                    <td><?= sanitize($cert['ExpiryDate'] ?? '') ?></td>
                    <td><span class="badge bg-warning text-dark"><?= max(0, (int)((strtotime($cert['ExpiryDate'] ?? 'now') - time()) / 86400)) ?>d</span></td>
                    <td><span class="badge bg-secondary"><?= sanitize($cert['Status'] ?? '') ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (count($expiringPermits) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-warning">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-file-earmark-check text-warning me-2"></i>Expiring Permits
            <span class="badge bg-warning text-dark ms-2"><?= count($expiringPermits) ?></span>
        </h6>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Permit</th><th>Expiry Date</th><th>Days Left</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($expiringPermits as $permit): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($permit['PermitName'] ?? $permit['Name'] ?? '') ?></td>
                    <td><?= sanitize($permit['ExpiryDate'] ?? '') ?></td>
                    <td><span class="badge bg-warning text-dark"><?= max(0, (int)((strtotime($permit['ExpiryDate'] ?? 'now') - time()) / 86400)) ?>d</span></td>
                    <td><span class="badge bg-secondary"><?= sanitize($permit['Status'] ?? '') ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (count($dueMaintenance) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 <?= count(array_filter($dueMaintenance, fn($m) => (int)($m['Overdue'] ?? 0)) > 0) ? 'border-danger' : 'border-info' ?>">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-wrench text-danger me-2"></i>Maintenance Reminders
            <span class="badge bg-danger ms-2"><?= count($dueMaintenance) ?></span>
        </h6>
        <p class="text-muted mb-2" style="font-size:0.78rem;">Scheduled within the next 7 days or overdue.</p>
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Machine</th><th>Type</th><th>Scheduled Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php foreach ($dueMaintenance as $m): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($m['MachineName'] ?? 'Unassigned') ?></td>
                    <td><?= sanitize($m['MaintenanceType'] ?? '') ?></td>
                    <td><?= sanitize($m['MaintenanceDate'] ?? '') ?></td>
                    <td>
                        <?php if ((int)($m['Overdue'] ?? 0) === 1): ?>
                        <span class="badge bg-danger">Overdue</span>
                        <?php else: ?>
                        <span class="badge bg-info text-dark">Due in <?= max(0, (int)ceil((strtotime((string)$m['MaintenanceDate']) - time()) / 86400)) ?>d</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
