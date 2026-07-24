<?php $pageTitle = 'Attendance'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-check me-2"></i><?= $pageTitle ?></h5>
    <a href="?route=staff" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Back to Staff</a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <input type="hidden" name="route" value="staff/attendance">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Filter by Date</label>
                <input type="date" name="date" class="form-control" value="<?= sanitize($selectedDate ?? date('Y-m-d')) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
            </div>
            <div class="col-md-2">
                <a href="?route=staff/attendance/form" class="btn btn-success w-100"><i class="bi bi-plus-lg"></i> Add</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>Staff</th><th>Date</th><th>Shift</th><th>Clock In</th><th>Clock Out</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php if (isset($attendanceRecords)): foreach ($attendanceRecords as $a): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize(($a['FirstName'] ?? '') . ' ' . ($a['LastName'] ?? '')) ?></td>
                    <td><?= sanitize($a['Date'] ?? '') ?></td>
                    <td><?= sanitize($a['ShiftName'] ?? $a['shift_name'] ?? '') ?></td>
                    <td><?= sanitize($a['ClockIn'] ?? $a['clock_in'] ?? '') ?></td>
                    <td><?= sanitize($a['ClockOut'] ?? $a['clock_out'] ?? '') ?></td>
                    <td>
                        <?php $st = $a['Status'] ?? ''; $map = ['Present'=>'success','Absent'=>'danger','Late'=>'warning','Leave'=>'info']; ?>
                        <span class="badge bg-<?= $map[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td>
                        <a href="?route=staff/attendance/edit&id=<?= urlencode($a['AttendanceID'] ?? $a['attendance_id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=staff/attendance/delete&id=<?= urlencode($a['AttendanceID'] ?? $a['attendance_id']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this record?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
