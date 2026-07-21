<?php $pageTitle = 'Packaging Materials'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-box me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=materials/packaging/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Packaging</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Type</th><th>Unit</th><th>Stock</th><th>Min Stock</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($materials as $m): ?>
                <tr>
                    <td><?= sanitize($m['PackageID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($m['Name']) ?></td>
                    <td><?= sanitize($m['Type'] ?? '') ?></td>
                    <td><?= sanitize($m['Unit'] ?? 'pcs') ?></td>
                    <td>
                        <span class="<?= (float)($m['CurrentStock'] ?? 0) <= (float)($m['MinStock'] ?? 0) ? 'text-danger fw-bold' : '' ?>"><?= number_format((float)($m['CurrentStock'] ?? 0), 1) ?></span>
                    </td>
                    <td><?= number_format((float)($m['MinStock'] ?? 0), 1) ?></td>
                    <td>
                        <?php $st = $m['Status'] ?? 'Active'; ?>
                        <span class="badge bg-<?= $st === 'Active' ? 'success' : 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td>
                        <a href="?route=materials/packaging/edit&id=<?= urlencode($m['PackageID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=materials/packaging/delete&id=<?= urlencode($m['PackageID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this packaging material?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
