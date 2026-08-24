<?php
$pageTitle = 'New Payroll Run';
$existingSet = array_flip($existing);
$missingSalary = array_filter($staff, fn ($s) => (float)($s['MonthlySalary'] ?? 0) <= 0);
$toGenerate = array_filter($staff, fn ($s) => (float)($s['MonthlySalary'] ?? 0) > 0 && !isset($existingSet[$s['StaffID']]));
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-calendar-plus me-2"></i>New Payroll Run</h5>
    <a href="?route=payroll" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Payroll</a>
</div>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="post" action="?route=payroll/generate">
            <?= csrfField() ?>
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small">Period Month</label>
                    <select name="month" class="form-select" required>
                        <?php foreach (PayrollModel::MONTH_NAMES as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $num === (int)$month ? 'selected' : '' ?>><?= sanitize($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Period Year</label>
                    <select name="year" class="form-select" required>
                        <?php for ($y = (int)date('Y') + 1; $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $y === (int)$year ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="text-muted small mb-1">Estimated run total</div>
                    <div class="fs-4 fw-bold text-success mb-2"><?= money($estimated) ?></div>
                    <button type="submit" class="btn btn-success" <?= count($toGenerate) === 0 ? 'disabled' : '' ?>>
                        <i class="bi bi-play-fill me-1"></i>Generate Payslips
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($missingSalary): ?>
<div class="alert alert-warning d-flex align-items-center shadow-sm">
    <i class="bi bi-exclamation-triangle fs-4 me-3"></i>
    <div>
        <strong><?= count($missingSalary) ?> staff member(s) have no salary set</strong> and will be skipped:
        <?= sanitize(implode(', ', array_map(fn ($s) => trim($s['FirstName'] . ' ' . $s['LastName']), array_slice($missingSalary, 0, 5)))) ?>
        <?= count($missingSalary) > 5 ? ' +' . (count($missingSalary) - 5) . ' more' : '' ?> &mdash;
        <a href="?route=payroll/settings">set their salary first</a>.
    </div>
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom-0 pt-3"><strong class="small text-muted text-uppercase">Active Staff Salaries</strong></div>
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>ID</th><th>Name</th><th>Department</th><th>Position</th><th>Monthly Salary</th><th>This Run</th></tr>
            </thead>
            <tbody>
                <?php foreach ($staff as $s): $has = isset($existingSet[$s['StaffID']]); $priced = (float)($s['MonthlySalary'] ?? 0) > 0; ?>
                <tr>
                    <td class="small text-muted"><?= sanitize($s['StaffID']) ?></td>
                    <td class="fw-semibold"><?= sanitize(trim($s['FirstName'] . ' ' . $s['LastName'])) ?></td>
                    <td><?= sanitize($s['Department'] ?? '') ?></td>
                    <td><?= sanitize($s['Position'] ?? '') ?></td>
                    <td><?= money($s['MonthlySalary']) ?></td>
                    <td>
                        <?php if ($has): ?><span class="badge bg-secondary">Already generated</span>
                        <?php elseif (!$priced): ?><span class="badge bg-warning text-dark">No salary set</span>
                        <?php else: ?><span class="badge bg-success">Will be created</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
