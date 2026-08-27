<?php $pageTitle = 'Workers'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Workers (Laborers)</h5>
    <div>
        <?php if (canCreate('workers')): ?>
        <a href="?route=workers/import" class="btn btn-outline-success btn-sm me-1"><i class="bi bi-file-earmark-excel me-1"></i>Import Excel</a>
        <a href="?route=workers/create" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Worker</a>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <span class="me-3"><i class="bi bi-people-fill text-primary me-1"></i><strong><?= $total ?></strong> total</span>
        <span><i class="bi bi-person-check-fill text-success me-1"></i><strong><?= $activeCount ?></strong> active</span>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Worker ID</th><th>Name</th><th>Position</th>
                        <th>Phone</th><th>Monthly Pay</th><th>Date Hired</th><th>Status</th>
                        <?php if (canEdit('workers')): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($workers as $w): ?>
                    <tr>
                        <td><code><?= sanitize($w['WorkerID']) ?></code></td>
                        <td class="fw-semibold"><?= sanitize($w['FirstName'] . ' ' . $w['LastName']) ?></td>
                        <td><?= sanitize($w['Position'] ?? '-') ?></td>
                        <td><?= sanitize($w['Phone'] ?? '-') ?></td>
                        <td><?= money((float)($w['MonthlyPay'] ?? 0)) ?></td>
                        <td><?= sanitize($w['DateHired'] ?? '-') ?></td>
                        <td>
                            <?php if (($w['Status'] ?? '') === 'Active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php elseif (($w['Status'] ?? '') === 'On Leave'): ?>
                                <span class="badge bg-warning text-dark">On Leave</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Terminated</span>
                            <?php endif; ?>
                        </td>
                        <?php if (canEdit('workers')): ?>
                        <td>
                            <a href="?route=workers/edit&id=<?= urlencode($w['WorkerID']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="?route=workers/delete&id=<?= urlencode($w['WorkerID']) ?>" class="btn btn-sm btn-outline-danger ms-1"
                               onclick="return confirm('Delete this worker?')" title="Delete"><i class="bi bi-trash"></i></a>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if (empty($workers)): ?>
<div class="alert alert-info mt-3"><i class="bi bi-info-circle me-1"></i>No workers yet. Add workers by form or import an Excel sheet.</div>
<?php endif; ?>
