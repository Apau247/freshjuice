<?php $pageTitle = 'Shifts'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-clock me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=staff" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Staff</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list me-2"></i>Shift List</span>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="collapse" data-bs-target="#shiftForm"><i class="bi bi-plus-lg"></i> Add Shift</button>
    </div>
    <div class="card-body">
        <form id="shiftForm" class="collapse mb-3 row g-3" method="POST" action="?route=staff/shifts/create">
            <?= csrfField() ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Shift Name <span class="text-danger">*</span></label>
                <input type="text" name="shift_name" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-lg"></i> Save</button>
            </div>
        </form>
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>ID</th><th>Shift Name</th><th>Start</th><th>End</th><th>Description</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if (isset($shifts)): foreach ($shifts as $sh): ?>
                <tr>
                    <td><?= sanitize($sh['ShiftID']) ?></td>
                    <td class="fw-semibold"><?= sanitize($sh['ShiftName'] ?? '') ?></td>
                    <td><?= sanitize($sh['StartTime'] ?? '') ?></td>
                    <td><?= sanitize($sh['EndTime'] ?? '') ?></td>
                    <td><?= sanitize($sh['Description'] ?? '') ?></td>
                    <td>
                        <a href="?route=staff/shifts/edit&id=<?= urlencode($sh['ShiftID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=staff/shifts/delete&id=<?= urlencode($sh['ShiftID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this shift?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (isset($editShift)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold"><i class="bi bi-pencil me-2"></i>Edit Shift</div>
    <div class="card-body">
        <form method="POST" action="?route=staff/shifts/edit&id=<?= urlencode($editShift['ShiftID']) ?>
            <?= csrfField() ?>" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Shift Name <span class="text-danger">*</span></label>
                <input type="text" name="shift_name" class="form-control" value="<?= sanitize($editShift['ShiftName'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Start Time <span class="text-danger">*</span></label>
                <input type="time" name="start_time" class="form-control" value="<?= sanitize($editShift['StartTime'] ?? '') ?>" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">End Time <span class="text-danger">*</span></label>
                <input type="time" name="end_time" class="form-control" value="<?= sanitize($editShift['EndTime'] ?? '') ?>" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Update</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
