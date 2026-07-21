<?php $pageTitle = isset($customer) ? 'Edit Customer' : 'New Customer'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-people me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=customers" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($customer) ? 'customers/edit&id=' . urlencode($customer['CustomerID']) : 'customers/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Customer Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= sanitize($customer['Name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contact Person</label>
                <input type="text" name="contact" class="form-control" value="<?= sanitize($customer['Contact'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="<?= sanitize($customer['Email'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= sanitize($customer['Phone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Customer Type</label>
                <select name="type" class="form-select">
                    <option value="Retailer" <?= (isset($customer) && ($customer['Type'] ?? '') === 'Retailer') ? 'selected' : '' ?>>Retailer</option>
                    <option value="Distributor" <?= (isset($customer) && ($customer['Type'] ?? '') === 'Distributor') ? 'selected' : '' ?>>Distributor</option>
                    <option value="Wholesaler" <?= (isset($customer) && ($customer['Type'] ?? '') === 'Wholesaler') ? 'selected' : '' ?>>Wholesaler</option>
                    <option value="Direct" <?= (isset($customer) && ($customer['Type'] ?? '') === 'Direct') ? 'selected' : '' ?>>Direct</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Address</label>
                <textarea name="address" class="form-control" rows="2"><?= sanitize($customer['Address'] ?? '') ?></textarea>
            </div>
            <?php if (isset($customer)): ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= ($customer['Status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= ($customer['Status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($customer) ? 'Update' : 'Create' ?> Customer</button>
                <a href="?route=customers" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
