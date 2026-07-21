<?php $pageTitle = isset($ppe) ? 'Edit PPE' : 'Issue PPE'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-shield-check me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=ppe" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($ppe) ? 'ppe/edit&id=' . urlencode($ppe['PpeId']) : 'ppe/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Staff <span class="text-danger">*</span></label>
                <select name="StaffID" class="form-select" required>
                    <option value="">Select Staff Member</option>
                    <?php if (!empty($staffList)): foreach ($staffList as $staff): ?>
                    <option value="<?= sanitize($staff['StaffID']) ?>" <?= (isset($ppe) && ($ppe['EmployeeName'] ?? '') === $staff['StaffID']) ? 'selected' : '' ?>><?= sanitize($staff['FirstName'] . ' ' . $staff['LastName']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Equipment Type <span class="text-danger">*</span></label>
                <input type="text" name="PPESource" class="form-control" value="<?= sanitize($ppe['EquipmentType'] ?? '') ?>" required placeholder="e.g. Hard Hat, Safety Boots, Gloves">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date Issued <span class="text-danger">*</span></label>
                <input type="date" name="DateIssued" class="form-control" value="<?= sanitize($ppe['IssueDate'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Expiry Date</label>
                <input type="date" name="ExpiryDate" class="form-control" value="<?= sanitize($ppe['ExpiryDate'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Condition <span class="text-danger">*</span></label>
                <select name="Condition" class="form-select" required>
                    <option value="">Select</option>
                    <?php $cond = $ppe['Condition'] ?? ''; ?>
                    <option value="New" <?= $cond === 'New' ? 'selected' : '' ?>>New</option>
                    <option value="Good" <?= $cond === 'Good' ? 'selected' : '' ?>>Good</option>
                    <option value="Fair" <?= $cond === 'Fair' ? 'selected' : '' ?>>Fair</option>
                    <option value="Poor" <?= $cond === 'Poor' ? 'selected' : '' ?>>Poor</option>
                    <option value="Damaged" <?= $cond === 'Damaged' ? 'selected' : '' ?>>Damaged</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Status</label>
                <select name="Status" class="form-select">
                    <?php $st = $ppe['Status'] ?? 'Active'; ?>
                    <option value="Active" <?= $st === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Expired" <?= $st === 'Expired' ? 'selected' : '' ?>>Expired</option>
                    <option value="Returned" <?= $st === 'Returned' ? 'selected' : '' ?>>Returned</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Notes</label>
                <input type="text" name="Notes" class="form-control" value="<?= sanitize($ppe['Notes'] ?? '') ?>">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($ppe) ? 'Update' : 'Create' ?> PPE Record</button>
                <a href="?route=ppe" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
