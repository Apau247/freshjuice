<?php
$pageTitle = 'Payroll';
$canEdit = canEdit('payroll');
$statusBadge = ['Paid' => 'success', 'Unpaid' => 'danger'];
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-cash-coin me-2"></i>Employer Payroll</h5>
    <div class="d-flex gap-2 flex-wrap">
        <?php if ($canEdit): ?>
        <a href="?route=payroll/generate" class="btn btn-success btn-sm"><i class="bi bi-calendar-plus me-1"></i>New Payroll Run</a>
        <a href="?route=payroll/settings" class="btn btn-outline-secondary btn-sm"><i class="bi bi-sliders me-1"></i>Salary Settings</a>
        <?php endif; ?>
        <a href="?route=payroll/report&month=<?= $month ?>&year=<?= $year ?>" class="btn btn-outline-primary btn-sm"><i class="bi bi-file-earmark-bar-graph me-1"></i>Payment Report</a>
    </div>
</div>

<?php /* Period filter */ ?>
<form method="get" action="" class="row g-2 align-items-end mb-3">
    <input type="hidden" name="route" value="payroll">
    <div class="col-auto">
        <label class="form-label small text-muted mb-1">Month</label>
        <select name="month" class="form-select form-select-sm">
            <?php foreach (PayrollModel::MONTH_NAMES as $num => $name): ?>
            <option value="<?= $num ?>" <?= $num === (int)$month ? 'selected' : '' ?>><?= sanitize($name) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-auto">
        <label class="form-label small text-muted mb-1">Year</label>
        <select name="year" class="form-select form-select-sm">
            <?php for ($y = (int)date('Y') + 1; $y >= 2020; $y--): ?>
            <option value="<?= $y ?>" <?= $y === (int)$year ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
        </select>
    </div>
    <div class="col-auto">
        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel me-1"></i>Show</button>
    </div>
</form>

<?php /* Summary cards */ ?>
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small fw-semibold text-uppercase">Total Payroll</div>
                <div class="fs-4 fw-bold"><?= money($summary['grand_total']) ?></div>
                <div class="text-muted small"><?= $summary['all_cnt'] ?> payslip<?= $summary['all_cnt'] === 1 ? '' : 's' ?> &mdash; <?= sanitize(PayrollController::monthName((int)$month)) ?> <?= $year ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="text-success small fw-semibold text-uppercase"><i class="bi bi-check-circle me-1"></i>Paid</div>
                <div class="fs-4 fw-bold text-success"><?= money($summary['paid_total']) ?></div>
                <div class="text-muted small"><?= $summary['paid_cnt'] ?> of <?= $summary['all_cnt'] ?> staff paid</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
            <div class="card-body">
                <div class="text-danger small fw-semibold text-uppercase"><i class="bi bi-hourglass-split me-1"></i>Not Paid</div>
                <div class="fs-4 fw-bold text-danger"><?= money($summary['unpaid_total']) ?></div>
                <div class="text-muted small"><?= $summary['unpaid_cnt'] ?> payment<?= $summary['unpaid_cnt'] === 1 ? '' : 's' ?> outstanding</div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Payslip</th><th>Staff</th><th>Department</th><th>Base Salary</th><th>Allowances</th>
                    <th>Deductions</th><th>Net Pay</th><th>Status</th><th>Paid On</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $r): ?>
                <tr>
                    <td class="small text-muted"><?= sanitize($r['PayrollID']) ?></td>
                    <td class="fw-semibold"><?= sanitize(trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? ''))) ?></td>
                    <td><?= sanitize($r['Department'] ?? '') ?></td>
                    <td><?= money($r['BaseSalary']) ?></td>
                    <td><?= money($r['Allowances']) ?></td>
                    <td><?= money($r['Deductions']) ?></td>
                    <td class="fw-bold"><?= money($r['NetPay']) ?></td>
                    <td><span class="badge bg-<?= $statusBadge[$r['Status']] ?? 'secondary' ?>"><?= sanitize($r['Status']) ?></span></td>
                    <td class="small">
                        <?php if (($r['Status'] ?? '') === 'Paid'): ?>
                            <?= sanitize($r['PaymentDate'] ?? '') ?>
                            <?php if (!empty($r['PaymentMethod'])): ?><span class="text-muted d-block"><?= sanitize($r['PaymentMethod']) ?></span><?php endif; ?>
                        <?php else: ?>&mdash;<?php endif; ?>
                    </td>
                    <td class="text-nowrap">
                        <?php if ($canEdit && ($r['Status'] ?? '') === 'Unpaid'): ?>
                        <button type="button" class="btn btn-sm btn-success pay-btn"
                                data-id="<?= sanitize($r['PayrollID']) ?>"
                                data-name="<?= sanitize(trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? ''))) ?>"
                                data-net="<?= number_format((float)$r['NetPay'], 2) ?>"
                                data-today="<?= sanitize($today) ?>">
                            <i class="bi bi-cash-stack"></i> Mark Paid
                        </button>
                        <?php elseif ($canEdit && ($r['Status'] ?? '') === 'Paid'): ?>
                        <a href="?route=payroll/revert&id=<?= urlencode($r['PayrollID']) ?>" class="btn btn-sm btn-outline-warning" title="Revert to Unpaid"><i class="bi bi-arrow-counterclockwise"></i></a>
                        <?php endif; ?>
                        <?php if ($canEdit): ?>
                        <a href="?route=payroll/edit&id=<?= urlencode($r['PayrollID']) ?>" class="btn btn-sm btn-outline-primary" title="Adjust payslip"><i class="bi bi-pencil"></i></a>
                        <?php if (($r['Status'] ?? '') !== 'Paid'): ?>
                        <a href="?route=payroll/delete&id=<?= urlencode($r['PayrollID']) ?>" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (!$records): ?>
                <tr><td colspan="10" class="text-center text-muted py-4">No payroll records for <?= sanitize(PayrollController::monthName((int)$month)) ?> <?= $year ?>.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($canEdit): ?>
<?php /* Mark-as-paid modal */ ?>
<div class="modal fade" id="payModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="?route=payroll/pay" class="modal-content">
            <?= csrfField() ?>
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Record Payment</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Paying <strong id="payStaffName"></strong> &mdash; <span class="fw-bold" id="payNet"></span></p>
                <input type="hidden" name="id" id="payId">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label small">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <?php foreach ($methods as $m): ?><option value="<?= sanitize($m) ?>"><?= sanitize($m) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" max="<?= sanitize($today) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Notes (optional)</label>
                        <input type="text" name="notes" class="form-control" maxlength="200" placeholder="e.g. Part salary advance deducted">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Confirm Payment</button>
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('payModal');
    document.querySelectorAll('.pay-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            modal.querySelector('#payId').value = btn.dataset.id;
            modal.querySelector('#payStaffName').textContent = btn.dataset.name;
            modal.querySelector('#payNet').textContent = 'GH\u20B5 ' + btn.dataset.net;
            modal.querySelector('[name=payment_date]').value = btn.dataset.today;
            bootstrap.Modal.getOrCreateInstance(modal).show();
        });
    });
});
</script>
<?php endif; ?>
