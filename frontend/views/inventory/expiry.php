<?php $pageTitle = 'Expiry Alerts'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-hourglass-split me-2"></i><?= $pageTitle ?></h5>
</div>

<?php
$total = count($expiredGoods) + count($expiringGoods) + count($expiredCerts) + count($expiringCerts) + count($expiringPermits);
$daysLeft = static fn(?string $date): int => $date ? max(0, (int)ceil((strtotime($date) - time()) / 86400)) : 0;
?>

<?php if ($total === 0): ?>
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-check-circle text-success" style="font-size:3rem;"></i>
        <h5 class="mt-3 text-muted">Nothing is expiring soon</h5>
        <p class="text-muted">No finished goods, certifications or permits are approaching their expiry date.</p>
    </div>
</div>
<?php endif; ?>

<?php if (count($expiredGoods) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-danger">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-x-octagon-fill text-danger me-2"></i>Expired Finished Goods
            <span class="badge bg-danger ms-2"><?= count($expiredGoods) ?></span>
        </h6>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>FG ID</th><th>Flavour</th><th>Batch</th><th>Expiry Date</th><th>Expired For</th><th>Qty On Hand</th></tr>
            </thead>
            <tbody>
                <?php foreach ($expiredGoods as $g): ?>
                <tr>
                    <td><?= sanitize($g['FG_ID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($g['Flavour']) ?></td>
                    <td><?= sanitize($g['BatchNumber'] ?? '-') ?></td>
                    <td><?= sanitize($g['ExpiryDate']) ?></td>
                    <td><span class="badge bg-danger"><?= abs($daysLeft($g['ExpiryDate'])) ?>d ago</span></td>
                    <td><?= number_format((float)$g['QuantityAvailable'], 2) ?> <?= sanitize($g['Unit']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php endif; ?>

<?php if (count($expiringGoods) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-warning">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-cup-straw text-warning me-2"></i>Finished Goods Expiring Within 30 Days
            <span class="badge bg-warning text-dark ms-2"><?= count($expiringGoods) ?></span>
        </h6>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>FG ID</th><th>Flavour</th><th>Batch</th><th>Expiry Date</th><th>Days Left</th><th>Qty On Hand</th></tr>
            </thead>
            <tbody>
                <?php foreach ($expiringGoods as $g): ?>
                <tr>
                    <td><?= sanitize($g['FG_ID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($g['Flavour']) ?></td>
                    <td><?= sanitize($g['BatchNumber'] ?? '-') ?></td>
                    <td><?= sanitize($g['ExpiryDate']) ?></td>
                    <td><span class="badge <?= $daysLeft($g['ExpiryDate']) <= 7 ? 'bg-danger' : 'bg-warning text-dark' ?>"><?= $daysLeft($g['ExpiryDate']) ?>d</span></td>
                    <td><?= number_format((float)$g['QuantityAvailable'], 2) ?> <?= sanitize($g['Unit']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php endif; ?>

<?php if (count($expiredCerts) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-danger">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-award text-danger me-2"></i>Expired Certifications
            <span class="badge bg-danger ms-2"><?= count($expiredCerts) ?></span>
        </h6>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Certificate</th><th>Type</th><th>Authority</th><th>Expiry Date</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($expiredCerts as $c): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($c['CertName']) ?></td>
                    <td><?= sanitize($c['CertType']) ?></td>
                    <td><?= sanitize($c['IssuingAuthority'] ?? '-') ?></td>
                    <td><?= sanitize($c['ExpiryDate']) ?></td>
                    <td><span class="badge bg-danger">Expired</span></td>
                    <td>
                        <?php if (canEdit('certifications')): ?>
                        <a href="?route=certifications/edit&id=<?= urlencode($c['CertID']) ?>" class="btn btn-sm btn-outline-primary" title="Renew"><i class="bi bi-arrow-repeat"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php endif; ?>

<?php if (count($expiringCerts) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-warning">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-award text-warning me-2"></i>Certifications Expiring Within 90 Days
            <span class="badge bg-warning text-dark ms-2"><?= count($expiringCerts) ?></span>
        </h6>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Certificate</th><th>Type</th><th>Authority</th><th>Expiry Date</th><th>Days Left</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($expiringCerts as $c): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($c['CertName']) ?></td>
                    <td><?= sanitize($c['CertType']) ?></td>
                    <td><?= sanitize($c['IssuingAuthority'] ?? '-') ?></td>
                    <td><?= sanitize($c['ExpiryDate']) ?></td>
                    <td><span class="badge <?= $daysLeft($c['ExpiryDate']) <= 30 ? 'bg-danger' : 'bg-warning text-dark' ?>"><?= $daysLeft($c['ExpiryDate']) ?>d</span></td>
                    <td><span class="badge bg-secondary"><?= sanitize($c['Status']) ?></span></td>
                    <td>
                        <?php if (canEdit('certifications')): ?>
                        <a href="?route=certifications/edit&id=<?= urlencode($c['CertID']) ?>" class="btn btn-sm btn-outline-primary" title="Renew"><i class="bi bi-arrow-repeat"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php endif; ?>

<?php if (count($expiringPermits) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-warning">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-file-earmark-check text-warning me-2"></i>Permits Expiring Within 60 Days
            <span class="badge bg-warning text-dark ms-2"><?= count($expiringPermits) ?></span>
        </h6>
        <div class="table-responsive"><table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Permit</th><th>Expiry Date</th><th>Days Left</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($expiringPermits as $p): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($p['PermitName'] ?? $p['Name'] ?? '') ?></td>
                    <td><?= sanitize($p['ExpiryDate']) ?></td>
                    <td><span class="badge <?= $daysLeft($p['ExpiryDate']) <= 14 ? 'bg-danger' : 'bg-warning text-dark' ?>"><?= $daysLeft($p['ExpiryDate']) ?>d</span></td>
                    <td><span class="badge bg-secondary"><?= sanitize($p['Status'] ?? '') ?></span></td>
                    <td>
                        <?php if (canEdit('permits')): ?>
                        <a href="?route=permits/edit&id=<?= urlencode($p['PermitID'] ?? '') ?>" class="btn btn-sm btn-outline-primary" title="Renew"><i class="bi bi-arrow-repeat"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table></div>
    </div>
</div>
<?php endif; ?>
