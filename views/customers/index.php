<?php $pageTitle = 'Customers'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-people me-2"></i><?= $pageTitle ?></h5>
    <?php if (canCreate('customers')): ?>
    <a href="?route=customers/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Customer</a>
    <?php endif; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Contact</th><th>Email</th><th>Phone</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($customers as $c): ?>
                <tr>
                    <td><?= sanitize($c['CustomerID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($c['Name']) ?></td>
                    <td><?= sanitize($c['Contact'] ?? '') ?></td>
                    <td><?= sanitize($c['Email'] ?? '') ?></td>
                    <td><?= sanitize($c['Phone'] ?? '') ?></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($c['Type'] ?? 'Retailer') ?></span></td>
                    <td>
                        <?php $st = $c['Status'] ?? 'Active'; ?>
                        <span class="badge bg-<?= $st === 'Active' ? 'success' : 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td>
                        <a href="?route=customers/edit&id=<?= urlencode($c['CustomerID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=customers/delete&id=<?= urlencode($c['CustomerID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this customer?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
