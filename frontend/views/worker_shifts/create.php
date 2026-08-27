<?php $pageTitle = 'Assign Worker to Shift'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-plus me-2"></i>Assign Worker to Shift</h5>
    <a href="?route=worker-shifts" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="?route=worker-shifts/store">
                    <?= csrfField() ?>
                    <div class="mb-3">
                        <label class="form-label">Worker</label>
                        <select name="WorkerID" class="form-select" required>
                            <option value="">Select worker...</option>
                            <?php foreach ($workers as $w): ?>
                            <?php if ($w['Status'] === 'Active'): ?>
                            <option value="<?= sanitize($w['WorkerID']) ?>"><?= sanitize($w['FirstName'] . ' ' . $w['LastName']) ?> — <?= sanitize($w['Position']) ?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shift</label>
                        <select name="ShiftID" class="form-select" required>
                            <option value="">Select shift...</option>
                            <?php foreach ($shifts as $s): ?>
                            <option value="<?= sanitize($s['ShiftID']) ?>"><?= sanitize($s['ShiftName']) ?> (<?= date('g:i A', strtotime($s['StartTime'])) ?> – <?= date('g:i A', strtotime($s['EndTime'])) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="ShiftDate" class="form-control" value="<?= sanitize(date('Y-m-d')) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="Status" class="form-select">
                            <option value="Scheduled">Scheduled</option>
                            <option value="Completed">Completed</option>
                            <option value="Swapped">Swapped</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i>Save Assignment</button>
                </form>
            </div>
        </div>
    </div>
</div>
