<?php $pageTitle = 'Low Stock Alerts'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-exclamation-octagon me-2"></i><?= $pageTitle ?></h5>
</div>

<?php $total = count($rawMaterials) + count($packaging); ?>

<?php if ($total === 0): ?>
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-check-circle text-success" style="font-size:3rem;"></i>
        <h5 class="mt-3 text-muted">All stock levels are healthy</h5>
        <p class="text-muted">No raw or packaging materials are at or below their minimum stock level.</p>
    </div>
</div>
<?php else: ?>
<div class="alert alert-warning d-flex align-items-center" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <div><strong><?= $total ?></strong> item<?= $total === 1 ? '' : 's' ?> need reordering. Suggested order quantity restores a 2&times; minimum-stock buffer.</div>
</div>
<?php endif; ?>

<?php if (count($rawMaterials) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-danger">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-bar-chart-steps text-danger me-2"></i>Raw Materials
            <span class="badge bg-danger ms-2"><?= count($rawMaterials) ?></span>
        </h6>
        <table id="dataTable" class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Material</th><th>Type</th><th>Current Stock</th><th>Min Stock</th><th>Suggested Reorder</th><th>Supplier</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($rawMaterials as $item): ?>
                <tr>
                    <td><?= sanitize($item['MaterialID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($item['Name']) ?></td>
                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?= sanitize($item['Type'] ?? '-') ?></span></td>
                    <td><span class="badge bg-danger"><?= number_format((float)$item['CurrentStock'], 2) ?> <?= sanitize($item['Unit']) ?></span></td>
                    <td><?= number_format((float)$item['MinStock'], 2) ?> <?= sanitize($item['Unit']) ?></td>
                    <td><strong class="text-success">+<?= number_format((float)$item['SuggestedReorder'], 2) ?> <?= sanitize($item['Unit']) ?></strong></td>
                    <td><?= sanitize($item['SupplierName'] ?? '-') ?></td>
                    <td>
                        <?php if (canEdit('materials')): ?>
                        <a href="?route=materials/raw/edit&id=<?= urlencode($item['MaterialID']) ?>" class="btn btn-sm btn-outline-primary" title="Restock / Edit"><i class="bi bi-pencil"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (count($packaging) > 0): ?>
<div class="card border-0 shadow-sm mb-3 border-start border-4 border-warning">
    <div class="card-body">
        <h6 class="fw-bold mb-3">
            <i class="bi bi-box text-warning me-2"></i>Packaging Materials
            <span class="badge bg-warning text-dark ms-2"><?= count($packaging) ?></span>
        </h6>
        <table id="dataTable2" class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>ID</th><th>Material</th><th>Type</th><th>Current Stock</th><th>Min Stock</th><th>Suggested Reorder</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($packaging as $item): ?>
                <tr>
                    <td><?= sanitize($item['PackageID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($item['Name']) ?></td>
                    <td><span class="badge bg-secondary bg-opacity-10 text-secondary"><?= sanitize($item['Type'] ?? '-') ?></span></td>
                    <td><span class="badge bg-danger"><?= number_format((float)$item['CurrentStock'], 2) ?> <?= sanitize($item['Unit']) ?></span></td>
                    <td><?= number_format((float)$item['MinStock'], 2) ?> <?= sanitize($item['Unit']) ?></td>
                    <td><strong class="text-success">+<?= number_format((float)$item['SuggestedReorder'], 2) ?> <?= sanitize($item['Unit']) ?></strong></td>
                    <td>
                        <?php if (canEdit('materials')): ?>
                        <a href="?route=materials/packaging/edit&id=<?= urlencode($item['PackageID']) ?>" class="btn btn-sm btn-outline-primary" title="Restock / Edit"><i class="bi bi-pencil"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
