<?php $pageTitle = isset($supplier) ? 'Edit Supplier' : 'New Supplier'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-truck me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=suppliers" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($supplier) ? 'suppliers/edit&id=' . urlencode($supplier['SupplierID']) : 'suppliers/create' ?>
            <?= csrfField() ?>" class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Supplier Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= sanitize($supplier['Name'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Contact Person</label>
                <input type="text" name="contact_person" class="form-control" value="<?= sanitize($supplier['Contact'] ?? $supplier['contact_person'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Email</label>
                <input type="email" name="email" class="form-control" value="<?= sanitize($supplier['Email'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= sanitize($supplier['Phone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Category / Type</label>
                <select name="category" class="form-select">
                    <option value="Fruit Supplier" <?= (($supplier['Type'] ?? $supplier['category'] ?? '') === 'Fruit Supplier') ? 'selected' : '' ?>>Fruit Supplier</option>
                    <option value="Packaging Supplier" <?= (($supplier['Type'] ?? $supplier['category'] ?? '') === 'Packaging Supplier') ? 'selected' : '' ?>>Packaging Supplier</option>
                    <option value="Chemical Supplier" <?= (($supplier['Type'] ?? $supplier['category'] ?? '') === 'Chemical Supplier') ? 'selected' : '' ?>>Chemical Supplier</option>
                    <option value="Equipment Supplier" <?= (($supplier['Type'] ?? $supplier['category'] ?? '') === 'Equipment Supplier') ? 'selected' : '' ?>>Equipment Supplier</option>
                    <option value="Other" <?= (($supplier['Type'] ?? $supplier['category'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Address</label>
                <textarea name="address" class="form-control" rows="2"><?= sanitize($supplier['Address'] ?? '') ?></textarea>
            </div>
            <?php if (isset($supplier)): ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?= ($supplier['Status'] ?? '') === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Inactive" <?= ($supplier['Status'] ?? '') === 'Inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <?php endif; ?>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($supplier) ? 'Update' : 'Create' ?> Supplier</button>
                <a href="?route=suppliers" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
