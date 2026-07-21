<?php $pageTitle = 'Users'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2"></i><?= $pageTitle ?></h5>
    <?php if (canCreate('users')): ?>
    <a href="?route=users/create" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New User</a>
    <?php endif; ?>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>User ID</th><th>Name</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= sanitize($u['UserID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($u['Name']) ?></td>
                    <td><span class="badge bg-primary bg-opacity-10 text-primary"><?= sanitize($u['RoleName'] ?? $u['role_name'] ?? '') ?></span></td>
                    <td>
                        <?php $st = $u['Status'] ?? 'Active'; ?>
                        <span class="badge bg-<?= $st === 'Active' ? 'success' : 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td><?= sanitize($u['created_at'] ?? '') ?></td>
                    <td>
                        <a href="?route=users/edit&id=<?= urlencode($u['UserID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=users/delete&id=<?= urlencode($u['UserID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
