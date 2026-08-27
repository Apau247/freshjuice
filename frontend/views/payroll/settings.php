<?php
$pageTitle = 'Salary Settings';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-sliders me-2"></i>Salary Settings</h5>
    <a href="?route=payroll" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Payroll</a>
</div>

<div class="alert alert-info shadow-sm small">
    <i class="bi bi-info-circle me-1"></i>
    Type a <strong>monthly pay amount (GH&#8373;)</strong>, then <strong>Pay</strong> to settle that staff member or worker for any month in one click &mdash; the amount is saved and the payslip marked PAID immediately.
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="?route=payroll/settings" id="salaryForm">
            <?= csrfField() ?>
            <input type="hidden" name="salaries" id="salariesJson">
            <div class="table-responsive"><table id="dataTable" class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>ID</th><th>Name</th><th>Group</th><th>Position</th><th>Status</th><th style="width:220px">Monthly Pay (GH&#8373;)</th><th class="text-end">Quick Pay</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($staff as $s): ?>
                    <?php $key = ($s['PersonType'] === 'worker' ? 'w:' : 's:') . $s['Id']; ?>
                    <tr data-person="<?= sanitize($key) ?>">
                        <td class="small text-muted"><?= sanitize($s['Id']) ?></td>
                        <td class="fw-semibold"><?= sanitize(trim($s['FirstName'] . ' ' . $s['LastName'])) ?></td>
                        <td>
                            <?php if ($s['PersonType'] === 'worker'): ?>
                                <span class="badge bg-dark">Worker</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Staff</span>
                            <?php endif; ?>
                        </td>
                        <td><?= sanitize($s['Position'] ?? '') ?></td>
                        <td><span class="badge bg-<?= $s['Status'] === 'Active' ? 'success' : 'info' ?>"><?= sanitize($s['Status']) ?></span></td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">GH&#8373;</span>
                                <input type="number" step="0.01" min="0" max="10000000"
                                       class="form-control salary-input"
                                       data-person="<?= sanitize($key) ?>"
                                       value="<?= number_format((float)($s['Pay'] ?? 0), 2, '.', '') ?>">
                            </div>
                            <?php if ((float)($s['Pay'] ?? 0) <= 0): ?>
                            <div class="form-text text-warning small">No pay set &mdash; will be skipped in payroll runs.</div>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-success quick-pay-btn"
                                    data-person="<?= sanitize($key) ?>"
                                    data-type="<?= sanitize($s['PersonType']) ?>"
                                    data-name="<?= sanitize(trim($s['FirstName'] . ' ' . $s['LastName'])) ?>"
                                    data-today="<?= date('Y-m-d') ?>">
                                <i class="bi bi-cash-coin me-1"></i>Pay
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table></div>
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save All Pay Rates</button>
                <button type="reset" class="btn btn-outline-secondary">Reset</button>
            </div>
        </form>
    </div>
</div>

<?php /* Quick-pay modal -- kept OUTSIDE #salaryForm (no nested forms) */ ?>
<div class="modal fade" id="quickPayModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="?route=payroll/pay-staff" class="modal-content">
            <?= csrfField() ?>
            <input type="hidden" name="person_type" id="qpPersonType" value="staff">
            <input type="hidden" name="person_id" id="qpPersonId">
            <div class="modal-header">
                <h6 class="modal-title"><i class="bi bi-cash-coin me-2"></i>Set &amp; Pay</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Paying <strong id="qpStaffName"></strong> for:</p>
                <div class="row g-3">
                    <div class="col-7">
                        <label class="form-label small">Month</label>
                        <select name="month" class="form-select">
                            <?php foreach (PayrollModel::MONTH_NAMES as $num => $name): ?>
                            <option value="<?= $num ?>" <?= $num === (int)date('n') ? 'selected' : '' ?>><?= sanitize($name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-5">
                        <label class="form-label small">Year</label>
                        <select name="year" class="form-select">
                            <?php for ($y = (int)date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= $y === (int)date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Monthly Pay Rate (GH&#8373;) &mdash; their rate; change it here to save a new rate</label>
                        <div class="input-group">
                            <span class="input-group-text">GH&#8373;</span>
                            <input type="number" step="0.01" min="0" max="10000000" name="salary" id="qpSalary" class="form-control" placeholder="Leave as is">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Amount to Pay (GH&#8373;) &mdash; the actual cash for this month; may differ from the salary</label>
                        <div class="input-group">
                            <span class="input-group-text">GH&#8373;</span>
                            <input type="number" step="0.01" min="0.01" max="10000000" name="amount" id="qpAmount" class="form-control" required>
                        </div>
                        <div class="form-text">Pay less than the salary for advances/part-payment, or more for bonuses &mdash; recorded automatically.</div>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option>Cash</option><option>Mobile Money</option><option>Bank Transfer</option><option>Cheque</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small">Payment Date</label>
                        <input type="date" name="payment_date" id="qpDate" class="form-control" max="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Notes (optional)</label>
                        <input type="text" name="notes" class="form-control" maxlength="200" placeholder="e.g. Salary advance deducted">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-check-lg me-1"></i>Save &amp; Pay</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Save All: collect every row's amount into one JSON payload */
    document.getElementById('salaryForm').addEventListener('submit', function () {
        var out = {};
        document.querySelectorAll('.salary-input').forEach(function (input) {
            out[input.dataset.person] = input.value;
        });
        document.getElementById('salariesJson').value = JSON.stringify(out);
    });

    /* Quick Pay modal: prefill from the row's live input */
    var modal = document.getElementById('quickPayModal');
    document.querySelectorAll('.quick-pay-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.querySelector('.salary-input[data-person="' + btn.dataset.person + '"]');
            modal.querySelector('#qpPersonType').value = btn.dataset.type;
            modal.querySelector('#qpPersonId').value = btn.dataset.person.replace(/^[sw]:/, '');
            modal.querySelector('#qpStaffName').textContent = btn.dataset.name;
            // Prefill both fields from the row's live value; they can then differ.
            var rowValue = input && parseFloat(input.value) > 0 ? input.value : '';
            modal.querySelector('#qpSalary').value = rowValue;
            modal.querySelector('#qpAmount').value = rowValue;
            modal.querySelector('#qpDate').value = btn.dataset.today;
            bootstrap.Modal.getOrCreateInstance(modal).show();
        });
    });
});
</script>
