<?php $isEdit = $mode === 'edit' && $worker; $pageTitle = $isEdit ? 'Edit Worker' : 'Add Worker'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-plus me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=workers" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=workers/<?= $isEdit ? 'update' : 'store' ?>" class="row g-3">
            <?= csrfField() ?>
            <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= sanitize($worker['WorkerID']) ?>">
            <?php endif; ?>

            <div class="col-md-4">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-control" required
                       value="<?= sanitize($worker['FirstName'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" required
                       value="<?= sanitize($worker['LastName'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control"
                       value="<?= sanitize($worker['Phone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Position</label>
                <input type="text" name="position" class="form-control" placeholder="e.g. Laborer, Machine Operator"
                       value="<?= sanitize($worker['Position'] ?? 'Laborer') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Monthly Pay (GH&#8373;)</label>
                <input type="number" name="monthly_pay" class="form-control" step="0.01" min="0"
                       value="<?= (float)($worker['MonthlyPay'] ?? 0) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Date Hired</label>
                <input type="date" name="date_hired" class="form-control"
                       value="<?= sanitize($worker['DateHired'] ?? '') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <?php foreach (['Active','On Leave','Terminated'] as $s): ?>
                    <option <?= ($worker['Status'] ?? 'Active') === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= $isEdit ? 'Update' : 'Create' ?> Worker</button>
                <a href="?route=workers" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
