<?php
$pageTitle = 'Salary Settings';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-sliders me-2"></i>Salary Settings</h5>
    <a href="?route=payroll" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Payroll</a>
</div>

<div class="alert alert-info shadow-sm small">
    <i class="bi bi-info-circle me-1"></i>
    Set each staff member's <strong>monthly payment amount (GH&#8373;)</strong>. New payroll runs copy this amount as the base salary. Only the Administrator and Factory Manager can change these.
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="?route=payroll/settings" id="salaryForm">
            <?= csrfField() ?>
            <input type="hidden" name="salaries" id="salariesJson">
            <table id="dataTable" class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>ID</th><th>Name</th><th>Department</th><th>Position</th><th>Status</th><th style="width:220px">Monthly Salary (GH&#8373;)</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $s): ?>
                    <tr data-staff="<?= sanitize($s['StaffID']) ?>">
                        <td class="small text-muted"><?= sanitize($s['StaffID']) ?></td>
                        <td class="fw-semibold"><?= sanitize(trim($s['FirstName'] . ' ' . $s['LastName'])) ?></td>
                        <td><?= sanitize($s['Department'] ?? '') ?></td>
                        <td><?= sanitize($s['Position'] ?? '') ?></td>
                        <td><span class="badge bg-<?= $s['Status'] === 'Active' ? 'success' : 'info' ?>"><?= sanitize($s['Status']) ?></span></td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">GH&#8373;</span>
                                <input type="number" step="0.01" min="0" max="10000000"
                                       class="form-control salary-input"
                                       data-staff="<?= sanitize($s['StaffID']) ?>"
                                       value="<?= number_format((float)($s['MonthlySalary'] ?? 0), 2, '.', '') ?>">
                            </div>
                            <?php if ((float)($s['MonthlySalary'] ?? 0) <= 0): ?>
                            <div class="form-text text-warning small">No salary set &mdash; will be skipped in payroll runs.</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save All Salaries</button>
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('salaryForm').addEventListener('submit', function () {
        var out = {};
        document.querySelectorAll('.salary-input').forEach(function (input) {
            out[input.dataset.staff] = input.value;
        });
        document.getElementById('salariesJson').value = JSON.stringify(out);
    });
});
</script>
