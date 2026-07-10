<?php $pageTitle = 'Staff'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge me-2"></i><?= $pageTitle ?></h5>
    <div>
        <a href="?route=staff/attendance" class="btn btn-outline-primary btn-sm me-1"><i class="bi bi-calendar-check"></i> Attendance</a>
        <a href="?route=staff/shifts" class="btn btn-outline-primary btn-sm me-1"><i class="bi bi-clock"></i> Shifts</a>
        <a href="?route=staff/form" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> New Staff</a>
    </div>
</div>
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light"><tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Department</th><th>Position</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($staffList as $s): ?>
                <tr>
                    <td><?= sanitize($s['StaffID']) ?></td>
                    <td class="fw-semibold"><?= sanitize(($s['FirstName'] ?? '') . ' ' . ($s['LastName'] ?? '')) ?></td>
                    <td><?= sanitize($s['Email'] ?? '') ?></td>
                    <td><?= sanitize($s['Phone'] ?? '') ?></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info"><?= sanitize($s['Department'] ?? '') ?></span></td>
                    <td><?= sanitize($s['Position'] ?? '') ?></td>
                    <td>
                        <?php $st = $s['Status'] ?? 'Active'; $map = ['Active'=>'success','On Leave'=>'warning','Terminated'=>'danger']; ?>
                        <span class="badge bg-<?= $map[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
                    </td>
                    <td>
                        <a href="?route=staff/edit&id=<?= urlencode($s['StaffID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="?route=staff/delete&id=<?= urlencode($s['StaffID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this staff member?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
