<?php $pageTitle = 'Finished Goods'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-cup-straw me-2"></i><?= $pageTitle ?></h5>
    <?php if (canCreate('finished_goods')): ?>
    <a href="?route=finished-goods/form" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Finished Good</a>
    <?php endif; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>FG ID</th><th>Batch#</th><th>Flavour</th><th>Qty Avail</th><th>Expiry</th><th>Location</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($goods as $g): ?>
                <?php $expiring = strtotime($g['ExpiryDate'] ?? '') <= strtotime('+30 days') && strtotime($g['ExpiryDate'] ?? '') >= time(); ?>
                <?php $expired = strtotime($g['ExpiryDate'] ?? '') < time(); ?>
                <tr class="<?= $expired ? 'table-danger' : ($expiring ? 'table-warning' : '') ?>">
                    <td><?= sanitize($g['FG_ID']) ?></td>
                    <td><?= sanitize($g['BatchNumber'] ?? $g['batch_number'] ?? '') ?></td>
                    <td class="fw-semibold"><?= sanitize($g['Flavour'] ?? '') ?></td>
                    <td><?= number_format((float)($g['QuantityAvailable'] ?? 0), 1) ?></td>
                    <td>
                        <?= sanitize($g['ExpiryDate'] ?? '') ?>
                        <?php if ($expired): ?><span class="badge bg-danger ms-1">Expired</span>
                        <?php elseif ($expiring): ?><span class="badge bg-warning text-dark ms-1">Expiring Soon</span>
                        <?php endif; ?>
                    </td>
                    <td><?= sanitize($g['StorageLocation'] ?? $g['storage_location'] ?? '') ?></td>
                    <td>
                        <?php if ($expired): ?><span class="badge bg-danger">Expired</span>
                        <?php elseif ($expiring): ?><span class="badge bg-warning text-dark">Warning</span>
                        <?php else: ?><span class="badge bg-success">Good</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="?route=finished-goods/edit&id=<?= urlencode($g['FG_ID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=finished-goods/delete&id=<?= urlencode($g['FG_ID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this finished good?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
