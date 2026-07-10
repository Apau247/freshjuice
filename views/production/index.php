<?php $pageTitle = 'Production Batches'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-gear me-2"></i><?= $pageTitle ?></h5>
    <?php if (canCreate('production')): ?>
    <a href="?route=production/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Batch</a>
    <?php endif; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Batch#</th><th>Date</th><th>Flavour</th><th>Qty</th><th>RM</th><th>PKG</th><th>Machine</th><th>Operator</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($batches as $b): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($b['BatchNumber'] ?? $b['BatchID'] ?? '') ?></td>
                    <td><?= sanitize($b['ProductionDate'] ?? '') ?></td>
                    <td><?= sanitize($b['Flavour'] ?? '') ?></td>
                    <td><?= number_format((float)($b['Quantity'] ?? 0), 1) ?> <?= sanitize($b['Unit'] ?? 'litres') ?></td>
                    <td><small class="text-muted"><?= sanitize($b['RawMaterialName'] ?? $b['raw_material_name'] ?? '') ?></small></td>
                    <td><small class="text-muted"><?= sanitize($b['PackagingName'] ?? $b['packaging_name'] ?? '') ?></small></td>
                    <td><?= sanitize($b['MachineName'] ?? $b['machine_name'] ?? '') ?></td>
                    <td><?= sanitize($b['UserName'] ?? $b['user_name'] ?? '') ?></td>
                    <td>
                        <?php $s = $b['Status'] ?? ''; $map = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Rejected'=>'danger','Cancelled'=>'secondary']; ?>
                        <span class="badge bg-<?= $map[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                    </td>
                    <td>
                        <a href="?route=production/edit&id=<?= urlencode($b['BatchID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=production/delete&id=<?= urlencode($b['BatchID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this batch?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
