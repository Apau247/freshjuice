<?php $pageTitle = isset($checklist) ? 'Edit Checklist' : 'New Checklist'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-check2-square me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=sops" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($checklist) ? 'sops/checklist/edit&id=' . urlencode($checklist['ChecklistID']) : 'sops/checklist/create' ?>
            <?= csrfField() ?>" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">SOP Template <span class="text-danger">*</span></label>
                <select name="sop_id" class="form-select" required>
                    <option value="">Select SOP</option>
                    <?php if (isset($templates)): foreach ($templates as $t): ?>
                    <option value="<?= sanitize($t['SOP_ID']) ?>" <?= (isset($checklist) && ($checklist['SOP_ID'] ?? '') === $t['SOP_ID']) ? 'selected' : '' ?>><?= sanitize($t['Title']) ?> (v<?= sanitize($t['Version'] ?? '1.0') ?>)</option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Batch</label>
                <select name="batch_id" class="form-select">
                    <option value="">Select Batch</option>
                    <?php if (isset($batches)): foreach ($batches as $b): ?>
                    <option value="<?= sanitize($b['BatchID']) ?>" <?= (isset($checklist) && ($checklist['BatchID'] ?? '') === $b['BatchID']) ? 'selected' : '' ?>><?= sanitize($b['BatchNumber'] ?? $b['BatchID']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="date" class="form-control" value="<?= sanitize($checklist['Date'] ?? $checklist['date'] ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Supervisor</label>
                <select name="supervisor_id" class="form-select">
                    <option value="">Select Supervisor</option>
                    <?php if (isset($users)): foreach ($users as $u): ?>
                    <option value="<?= sanitize($u['UserID']) ?>" <?= (isset($checklist) && ($checklist['SupervisorID'] ?? '') === $u['UserID']) ? 'selected' : '' ?>><?= sanitize($u['Name']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Total Items</label>
                <input type="number" name="total_items" class="form-control" value="<?= sanitize((string)($checklist['TotalItems'] ?? '0')) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Completed Items</label>
                <input type="number" name="completed_items" class="form-control" value="<?= sanitize((string)($checklist['CompletedItems'] ?? '0')) ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Approval Status</label>
                <select name="approval_status" class="form-select">
                    <option value="Pending" <?= (isset($checklist) && ($checklist['ApprovalStatus'] ?? '') === 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= (isset($checklist) && ($checklist['ApprovalStatus'] ?? '') === 'Approved') ? 'selected' : '' ?>>Approved</option>
                    <option value="Rejected" <?= (isset($checklist) && ($checklist['ApprovalStatus'] ?? '') === 'Rejected') ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?= sanitize($checklist['Notes'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($checklist) ? 'Update' : 'Create' ?> Checklist</button>
                <a href="?route=sops" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
