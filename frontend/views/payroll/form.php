<?php
$pageTitle = 'Adjust Payslip';
$isPaid = ($slip['Status'] ?? '') === 'Unpaid' ? false : true;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Adjust Payslip &mdash; <?= sanitize(trim($slip['FirstName'] . ' ' . $slip['LastName'])) ?></h5>
    <a href="?route=payroll&month=<?= (int)$slip['PeriodMonth'] ?>&year=<?= (int)$slip['PeriodYear'] ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Payroll</a>
</div>

<?php if ($isPaid): ?>
<div class="alert alert-info shadow-sm">
    <i class="bi bi-lock-fill me-2"></i>This payslip is <strong>PAID</strong> and locked. Revert the payment on the payroll page before making changes.
</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="post" action="?route=payroll/edit&id=<?= urlencode($slip['PayrollID']) ?>">
            <?= csrfField() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small">Staff</label>
                    <input type="text" class="form-control" value="<?= sanitize(trim($slip['FirstName'] . ' ' . $slip['LastName'])) ?> (<?= sanitize($slip['Department'] ?? '') ?>)" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Period</label>
                    <input type="text" class="form-control" value="<?= sanitize(PayrollController::monthName((int)$slip['PeriodMonth'])) ?> <?= (int)$slip['PeriodYear'] ?>" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <input type="text" class="form-control fw-bold <?= $isPaid ? 'text-success' : 'text-danger' ?>" value="<?= sanitize($slip['Status']) ?>" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Base Salary (GH&#8373;)</label>
                    <input type="number" step="0.01" min="0" max="1000000" name="base_salary" id="base" class="form-control"
                           value="<?= number_format((float)$slip['BaseSalary'], 2, '.', '') ?>" <?= $isPaid ? 'readonly' : 'required' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Allowances / Bonus (GH&#8373;)</label>
                    <input type="number" step="0.01" min="0" max="1000000" name="allowances" id="allow" class="form-control"
                           value="<?= number_format((float)$slip['Allowances'], 2, '.', '') ?>" <?= $isPaid ? 'readonly' : 'required' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Deductions (GH&#8373;)</label>
                    <input type="number" step="0.01" min="0" max="1000000" name="deductions" id="deduct" class="form-control"
                           value="<?= number_format((float)$slip['Deductions'], 2, '.', '') ?>" <?= $isPaid ? 'readonly' : 'required' ?>>
                </div>
                <div class="col-md-8">
                    <label class="form-label small">Notes (optional)</label>
                    <input type="text" name="notes" class="form-control" maxlength="500"
                           value="<?= sanitize($slip['Notes'] ?? '') ?>" placeholder="e.g. Mid-month advance deducted" <?= $isPaid ? 'readonly' : '' ?>>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Net Pay</label>
                    <div class="fs-3 fw-bold text-success" id="netpay"><?= money($slip['NetPay']) ?></div>
                </div>
            </div>
            <?php if (!$isPaid): ?>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save Changes</button>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if (!$isPaid): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var fmt = function (n) { return 'GH\u20B5 ' + n.toLocaleString('en-GH', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); };
    var recalc = function () {
        var base = parseFloat(document.getElementById('base').value) || 0;
        var allow = parseFloat(document.getElementById('allow').value) || 0;
        var ded = parseFloat(document.getElementById('deduct').value) || 0;
        document.getElementById('netpay').textContent = fmt(Math.max(base + allow - ded, 0));
    };
    ['base', 'allow', 'deduct'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', recalc);
    });
});
</script>
<?php endif; ?>
