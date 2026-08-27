<?php $isEdit = isset($record); $pageTitle = $isEdit ? 'Edit Attendance' : 'Record Attendance'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=staff/attendance" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '?route=staff/attendance/edit&id=' . urlencode($record['AttendanceID']) : '?route=staff/attendance/form' ?>" class="row g-3">
            <?= csrfField() ?>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Staff <span class="text-danger">*</span></label>
                <select name="StaffID" class="form-select" required>
                    <option value="">Select Staff</option>
                    <?php if (isset($staffList)): foreach ($staffList as $s): ?>
                    <option value="<?= sanitize($s['StaffID']) ?>" <?= (isset($record) && ($record['StaffID'] ?? '') === $s['StaffID']) ? 'selected' : '' ?>><?= sanitize($s['FirstName'] . ' ' . $s['LastName']) ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                <input type="date" name="Date" class="form-control" value="<?= sanitize($record['Date'] ?? $selectedDate ?? date('Y-m-d')) ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Shift</label>
                <select name="ShiftID" class="form-select">
                    <option value="">No Shift</option>
                    <?php if (isset($shifts)): foreach ($shifts as $sh): ?>
                    <option value="<?= sanitize($sh['ShiftID']) ?>" <?= (isset($record) && ($record['ShiftID'] ?? '') === $sh['ShiftID']) ? 'selected' : '' ?>><?= sanitize($sh['ShiftName']) ?> (<?= sanitize($sh['StartTime']) ?>-<?= sanitize($sh['EndTime']) ?>)</option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Clock In</label>
                <input type="time" name="ClockIn" class="form-control" value="<?= sanitize($record['ClockIn'] ?? '') ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="Status" class="form-select" required>
                    <?php $st = $record['Status'] ?? 'Present'; ?>
                    <option value="Present" <?= $st === 'Present' ? 'selected' : '' ?>>Present</option>
                    <option value="Absent" <?= $st === 'Absent' ? 'selected' : '' ?>>Absent</option>
                    <option value="Late" <?= $st === 'Late' ? 'selected' : '' ?>>Late</option>
                    <option value="Leave" <?= $st === 'Leave' ? 'selected' : '' ?>>Leave</option>
                </select>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> <?= $isEdit ? 'Update' : 'Record' ?> Attendance</button>
                <a href="?route=staff/attendance" class="btn btn-outline-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
