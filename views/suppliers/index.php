<?php $pageTitle = 'Suppliers'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-truck me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=suppliers/form" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Supplier</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Contact</th><th>Email</th><th>Phone</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($suppliers as $s): ?>
                <tr>
                    <td><?= sanitize($s['SupplierID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($s['Name']) ?></td>
                    <td><?= sanitize($s['Contact'] ?? $s['contact_person'] ?? '') ?></td>
                    <td><?= sanitize($s['Email'] ?? '') ?></td>
                    <td><?= sanitize($s['Phone'] ?? '') ?></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($s['Type'] ?? $s['category'] ?? '') ?></span></td>
                    <td>
                        <?php $st = $s['Status'] ?? 'Active'; ?>
                        <span class="badge bg-<?= $st === 'Active' ? 'success' : 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td>
                        <a href="?route=suppliers/edit&id=<?= urlencode($s['SupplierID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=suppliers/delete&id=<?= urlencode($s['SupplierID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this supplier?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
