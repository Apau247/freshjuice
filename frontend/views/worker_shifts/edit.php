<?php $pageTitle = 'Edit Shift Assignment'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-pencil me-2"></i>Edit Shift Assignment</h5>
    <a href="?route=worker-shifts" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="post" action="?route=worker-shifts/update">
                    <?= csrfField() ?>
                    <input type="hidden" name="id" value="<?= sanitize($assignment['AssignmentID']) ?>">
                    <div class="mb-3">
                        <label class="form-label">Worker</label>
                        <select name="WorkerID" class="form-select" required>
                            <?php foreach ($workers as $w): ?>
                            <option value="<?= sanitize($w['WorkerID']) ?>" <?= $w['WorkerID'] === $assignment['WorkerID'] ? 'selected' : '' ?>>
                                <?= sanitize($w['FirstName'] . ' ' . $w['LastName']) ?> — <?= sanitize($w['Position']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Shift</label>
                        <select name="ShiftID" class="form-select" required>
                            <?php foreach ($shifts as $s): ?>
                            <option value="<?= sanitize($s['ShiftID']) ?>" <?= $s['ShiftID'] === $assignment['ShiftID'] ? 'selected' : '' ?>>
                                <?= sanitize($s['ShiftName']) ?> (<?= date('g:i A', strtotime($s['StartTime'])) ?> – <?= date('g:i A', strtotime($s['EndTime'])) ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" name="ShiftDate" class="form-control" value="<?= sanitize($assignment['ShiftDate']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="Status" class="form-select">
                            <?php foreach (['Scheduled','Completed','Absent','Swapped'] as $st): ?>
                            <option value="<?= $st ?>" <?= $assignment['Status'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-lg me-1"></i>Update Assignment</button>
                </form>
            </div>
        </div>
    </div>
</div>
