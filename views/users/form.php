<?php $pageTitle = isset($user) ? 'Edit User' : 'New User'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-shield-lock me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=users" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($user) ? 'users/edit&id=' . urlencode($user['UserID']) : 'users/create' ?>
            <?= csrfField() ?>" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">User ID <span class="text-danger">*</span></label>
                <input type="text" name="user_id" class="form-control" value="<?= sanitize($user['UserID'] ?? '') ?>" <?= isset($user) ? 'readonly' : 'required' ?>>
                <?php if (isset($user)): ?><small class="text-muted">User ID cannot be changed</small><?php endif; ?>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= sanitize($user['Name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                <select name="role_id" class="form-select" required>
                    <option value="">Select Role</option>
                    <?php if (isset($roles)): foreach ($roles as $r): ?>
                    <option value="<?= sanitize($r['RoleID']) ?>" <?= (isset($user) && ($user['RoleID'] ?? '') === $r['RoleID']) ? 'selected' : '' ?>><?= sanitize($r['RoleName']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Password <?= isset($user) ? '(leave blank to keep)' : '<span class="text-danger">*</span>' ?></label>
                <input type="password" name="password" class="form-control" <?= isset($user) ? '' : 'required' ?>>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= (isset($user) && ($user['Status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= (isset($user) && ($user['Status'] ?? '') === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($user) ? 'Update' : 'Create' ?> User</button>
                <a href="?route=users" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
