<?php $pageTitle = 'Certifications'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-award me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=certifications/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Certification</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>ID</th><th>Name</th><th>Type</th><th>Authority</th><th>Issue Date</th><th>Expiry</th><th>Status</th><th>Days Left</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($certifications as $c): ?>
                <?php 
                    $exp = strtotime($c['ExpiryDate'] ?? '');
                    $now = time();
                    $daysLeft = $exp ? floor(($exp - $now) / 86400) : 0;
                    $expired = $exp && $exp < $now;
                    $expiring = !$expired && $daysLeft <= 90;
                ?>
                <tr class="<?= $expired ? 'table-danger' : ($expiring ? 'table-warning' : '') ?>">
                    <td><?= sanitize($c['CertID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($c['CertName'] ?? '') ?></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($c['CertType'] ?? '') ?></span></td>
                    <td><?= sanitize($c['IssuingAuthority'] ?? $c['issuing_authority'] ?? '') ?></td>
                    <td><?= sanitize($c['IssueDate'] ?? '') ?></td>
                    <td><?= sanitize($c['ExpiryDate'] ?? '') ?></td>
                    <td>
                        <?php $st = $c['Status'] ?? ''; $map = ['Active'=>'success','Expired'=>'danger','Pending Renewal'=>'warning']; ?>
                        <span class="badge bg-<?= $map[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td>
                        <?php if ($expired): ?><span class="badge bg-danger">Expired</span>
                        <?php elseif ($expiring): ?><span class="badge bg-warning text-dark"><?= $daysLeft ?> days</span>
                        <?php else: ?><span class="text-muted"><?= $daysLeft ?> days</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?route=certifications/edit&id=<?= urlencode($c['CertID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=certifications/delete&id=<?= urlencode($c['CertID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this certification?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
