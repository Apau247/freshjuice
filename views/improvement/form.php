<?php $pageTitle = isset($initiative) ? 'Edit Initiative' : 'New Initiative'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-lightbulb me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=improvement" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="?route=<?= isset($initiative) ? 'improvement/edit&id=' . urlencode($initiative['InitiativeID']) : 'improvement/create' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" value="<?= sanitize($initiative['Title'] ?? '') ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Category</label>
                <select name="category" class="form-select">
                    <option value="">Select Category</option>
                    <?php $cat = $initiative['Category'] ?? ''; ?>
                    <option value="Quality" <?= $cat === 'Quality' ? 'selected' : '' ?>>Quality</option>
                    <option value="Safety" <?= $cat === 'Safety' ? 'selected' : '' ?>>Safety</option>
                    <option value="Efficiency" <?= $cat === 'Efficiency' ? 'selected' : '' ?>>Efficiency</option>
                    <option value="Cost Reduction" <?= $cat === 'Cost Reduction' ? 'selected' : '' ?>>Cost Reduction</option>
                    <option value="Process" <?= $cat === 'Process' ? 'selected' : '' ?>>Process</option>
                    <option value="Other" <?= $cat === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= sanitize($initiative['Description'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Root Cause Analysis</label>
                <textarea name="root_cause" class="form-control" rows="3"><?= sanitize($initiative['RootCauseAnalysis'] ?? '') ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Action Plan</label>
                <textarea name="action_plan" class="form-control" rows="3"><?= sanitize($initiative['ActionPlan'] ?? '') ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Target Date</label>
                <input type="date" name="target_date" class="form-control" value="<?= sanitize($initiative['TargetDate'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Responsible Person</label>
                <input type="text" name="responsible_person" class="form-control" value="<?= sanitize($initiative['ResponsiblePerson'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <?php $st = $initiative['Status'] ?? 'Proposed'; ?>
                    <option value="Proposed" <?= $st === 'Proposed' ? 'selected' : '' ?>>Proposed</option>
                    <option value="Approved" <?= $st === 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="In Progress" <?= $st === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Completed" <?= $st === 'Completed' ? 'selected' : '' ?>>Completed</option>
                    <option value="Cancelled" <?= $st === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Effectiveness / Notes</label>
                <textarea name="effectiveness" class="form-control" rows="2"><?= sanitize($initiative['Effectiveness'] ?? '') ?></textarea>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= isset($initiative) ? 'Update' : 'Create' ?> Initiative</button>
                <a href="?route=improvement" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
