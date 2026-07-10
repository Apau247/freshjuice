<?php $pageTitle = isset($staff) ? 'Edit Staff' : 'New Staff'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=staff" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($staff) ? 'staff/edit&id=' . urlencode($staff['StaffID']) : 'staff/create' ?>" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                <input type="text" name="first_name" class="form-control" value="<?= sanitize($staff['FirstName'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                <input type="text" name="last_name" class="form-control" value="<?= sanitize($staff['LastName'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="<?= sanitize($staff['Email'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= sanitize($staff['Phone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Department</label>
                <select name="department" class="form-select">
                    <option value="">Select Department</option>
                    <option value="Production" <?= (isset($staff) && ($staff['Department'] ?? '') === 'Production') ? 'selected' : '' ?>>Production</option>
                    <option value="Quality" <?= (isset($staff) && ($staff['Department'] ?? '') === 'Quality') ? 'selected' : '' ?>>Quality</option>
                    <option value="Maintenance" <?= (isset($staff) && ($staff['Department'] ?? '') === 'Maintenance') ? 'selected' : '' ?>>Maintenance</option>
                    <option value="Logistics" <?= (isset($staff) && ($staff['Department'] ?? '') === 'Logistics') ? 'selected' : '' ?>>Logistics</option>
                    <option value="Administration" <?= (isset($staff) && ($staff['Department'] ?? '') === 'Administration') ? 'selected' : '' ?>>Administration</option>
                    <option value="Sales" <?= (isset($staff) && ($staff['Department'] ?? '') === 'Sales') ? 'selected' : '' ?>>Sales</option>
                    <option value="Management" <?= (isset($staff) && ($staff['Department'] ?? '') === 'Management') ? 'selected' : '' ?>>Management</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Position</label>
                <input type="text" name="position" class="form-control" value="<?= sanitize($staff['Position'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date Hired</label>
                <input type="date" name="date_hired" class="form-control" value="<?= sanitize($staff['DateHired'] ?? $staff['date_hired'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= (isset($staff) && ($staff['Status'] ?? '') === 'Active') ? 'selected' : '' ?>>Active</option>
                    <option value="On Leave" <?= (isset($staff) && ($staff['Status'] ?? '') === 'On Leave') ? 'selected' : '' ?>>On Leave</option>
                    <option value="Terminated" <?= (isset($staff) && ($staff['Status'] ?? '') === 'Terminated') ? 'selected' : '' ?>>Terminated</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($staff) ? 'Update' : 'Create' ?> Staff</button>
                <a href="?route=staff" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
