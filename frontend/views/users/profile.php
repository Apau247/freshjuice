<?php $pageTitle = 'My Profile'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-circle me-2"></i><?= $pageTitle ?></h5>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center" style="width:80px;height:80px;border-radius:20px;background:var(--gradient-brand);color:white;font-size:2rem;font-weight:800;">
                    <?= strtoupper(substr($profileUser['Name'], 0, 1)) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= sanitize($profileUser['Name']) ?></h5>
                <p class="text-muted mb-2" style="font-size:0.85rem;"><?= sanitize($profileUser['Email'] ?? '') ?></p>
                <span class="badge bg-primary bg-opacity-10 text-primary"><?= sanitize($profileUser['RoleName'] ?? $profileUser['RoleID'] ?? '') ?></span>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-key me-2"></i>Change Password</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="?route=profile">
                    <?= csrfField() ?>
                    <input type="hidden" name="form_action" value="change_password">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100"><i class="bi bi-shield-lock"></i> Change Password</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-3 pb-0">
                <h6 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i>Update Profile</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="?route=profile" class="row g-3">
                    <?= csrfField() ?>
                    <input type="hidden" name="form_action" value="update_profile">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= sanitize($profileUser['Name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= sanitize($profileUser['Email'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">User ID</label>
                        <input type="text" class="form-control" value="<?= sanitize($profileUser['UserID']) ?>" readonly disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Role</label>
                        <input type="text" class="form-control" value="<?= sanitize($profileUser['RoleName'] ?? $profileUser['RoleID'] ?? '') ?>" readonly disabled>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
