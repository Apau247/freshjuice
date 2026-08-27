<?php $pageTitle = 'Worker Shift Schedule'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>Worker Shift Schedule</h5>
    <div class="d-flex gap-2">
        <?php if (canCreate('workers')): ?>
        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#bulkAssignModal"><i class="bi bi-plus-lg me-1"></i>Bulk Assign</button>
        <a href="?route=worker-shifts/create" class="btn btn-sm btn-outline-success"><i class="bi bi-person-plus me-1"></i>Single Assign</a>
        <?php endif; ?>
    </div>
</div>

<!-- Today Summary -->
<div class="kpi-grid mb-3">
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-3">
            <div style="font-size:1.6rem;font-weight:700;color:var(--brand);"><?= $todaySummary['total'] ?></div>
            <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;">Total Today</div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-3">
            <div style="font-size:1.6rem;font-weight:700;color:#f59e0b;"><?= $todaySummary['Morning'] ?></div>
            <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;">Morning</div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-3">
            <div style="font-size:1.6rem;font-weight:700;color:#0ea5e9;"><?= $todaySummary['Afternoon'] ?></div>
            <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;">Afternoon</div>
        </div>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center p-3">
            <div style="font-size:1.6rem;font-weight:700;color:#8b5cf6;"><?= $todaySummary['Night'] ?></div>
            <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;">Night</div>
        </div>
    </div>
</div>

<!-- Date Range Filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="get" class="d-flex align-items-center gap-2 flex-wrap">
            <input type="hidden" name="route" value="worker-shifts">
            <label class="form-label mb-0 me-1" style="font-size:.75rem;">Range:</label>
            <input type="date" name="from" value="<?= sanitize($from) ?>" class="form-control form-control-sm" style="width:auto;">
            <span class="text-muted">to</span>
            <input type="date" name="to" value="<?= sanitize($to) ?>" class="form-control form-control-sm" style="width:auto;">
            <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-funnel me-1"></i>Filter</button>
            <a href="?route=worker-shifts" class="btn btn-sm btn-outline-secondary">Today</a>
        </form>
    </div>
</div>

<!-- Schedule Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($assignments)): ?>
            <div class="text-center py-4 text-muted">
                <i class="bi bi-calendar3" style="font-size:2rem;"></i>
                <p class="mt-2">No shift assignments found for this period.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Worker</th>
                            <th>Position</th>
                            <th>Shift</th>
                            <th>Time</th>
                            <th>Status</th>
                            <?php if (canEdit('workers')): ?><th>Actions</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $a): ?>
                        <tr>
                            <td><span class="fw-semibold"><?= date('D, M j', strtotime($a['ShiftDate'])) ?></span></td>
                            <td><?= sanitize($a['FirstName'] . ' ' . $a['LastName']) ?></td>
                            <td><span class="text-muted"><?= sanitize($a['Position']) ?></span></td>
                            <td>
                                <?php
                                $colors = ['Morning' => 'warning', 'Afternoon' => 'info', 'Night' => 'primary'];
                                $color = $colors[$a['ShiftName']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $color ?>"><?= sanitize($a['ShiftName']) ?></span>
                            </td>
                            <td style="font-size:.8rem;"><?= date('g:i A', strtotime($a['StartTime'])) ?> – <?= date('g:i A', strtotime($a['EndTime'])) ?></td>
                            <td>
                                <?php
                                $statusColors = ['Scheduled' => 'info', 'Completed' => 'success', 'Absent' => 'danger', 'Swapped' => 'warning'];
                                $sc = $statusColors[$a['Status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $sc ?>"><?= sanitize($a['Status']) ?></span>
                            </td>
                            <?php if (canEdit('workers')): ?>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="?route=worker-shifts/edit&id=<?= sanitize($a['AssignmentID']) ?>" class="btn btn-outline-primary btn-sm" style="padding:.2rem .5rem;font-size:.72rem;"><i class="bi bi-pencil"></i></a>
                                    <form method="post" action="?route=worker-shifts/delete" class="d-inline" onsubmit="return confirm('Delete this assignment?')">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="id" value="<?= sanitize($a['AssignmentID']) ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm" style="padding:.2rem .5rem;font-size:.72rem;"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Bulk Assign Modal -->
<?php if (canCreate('workers')): ?>
<div class="modal fade" id="bulkAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <form method="post" action="?route=worker-shifts/bulk">
                <?= csrfField() ?>
                <div class="modal-header">
                    <h6 class="fw-bold"><i class="bi bi-people me-2"></i>Bulk Assign Workers to Shift</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Shift</label>
                            <select name="ShiftID" class="form-select form-select-sm" required>
                                <option value="">Select shift...</option>
                                <?php foreach ($shifts as $s): ?>
                                <option value="<?= sanitize($s['ShiftID']) ?>"><?= sanitize($s['ShiftName']) ?> (<?= date('g:i A', strtotime($s['StartTime'])) ?> – <?= date('g:i A', strtotime($s['EndTime'])) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="ShiftDate" value="<?= sanitize(date('Y-m-d')) ?>" class="form-control form-control-sm" required>
                        </div>
                    </div>
                    <label class="form-label">Select Workers</label>
                    <div style="max-height:250px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:10px;padding:.5rem;">
                        <?php foreach ($workers as $w): ?>
                        <?php if ($w['Status'] === 'Active'): ?>
                        <div class="form-check py-1 border-bottom" style="font-size:.85rem;">
                            <input class="form-check-input" type="checkbox" name="worker_ids[]" value="<?= sanitize($w['WorkerID']) ?>" id="w_<?= sanitize($w['WorkerID']) ?>">
                            <label class="form-check-label" for="w_<?= sanitize($w['WorkerID']) ?>">
                                <?= sanitize($w['FirstName'] . ' ' . $w['LastName']) ?>
                                <span class="text-muted">— <?= sanitize($w['Position']) ?></span>
                            </label>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="form-check mt-2" style="font-size:.8rem;">
                        <input class="form-check-input" type="checkbox" id="selectAll" onclick="document.querySelectorAll('[name^=worker_]').forEach(c=>c.checked=this.checked)">
                        <label class="form-check-label" for="selectAll">Select all active workers</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Assign Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>
