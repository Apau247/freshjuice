<?php
$pageTitle = 'Payroll Payment Report';
$pctPaid = $summary['all_cnt'] > 0 ? round($summary['paid_cnt'] / $summary['all_cnt'] * 100) : 0;
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i>Payment Report &mdash; <?= sanitize(PayrollController::monthName((int)$month)) ?> <?= $year ?></h5>
    <div class="d-flex gap-2">
        <a href="?route=payroll/report&month=<?= $month ?>&year=<?= $year ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Payroll</a>
        <a href="?route=payroll/report/print&month=<?= $month ?>&year=<?= $year ?>" target="_blank" class="btn btn-dark btn-sm"><i class="bi bi-printer me-1"></i>Print</a>
    </div>
</div>

<form method="get" action="" class="row g-2 align-items-end mb-3">
    <input type="hidden" name="route" value="payroll/report">
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
    <div class="col-auto"><button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel me-1"></i>Show</button></div>
</form>

<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
            <div class="card-body">
                <div class="text-success small fw-semibold text-uppercase">Paid</div>
                <div class="fs-4 fw-bold text-success"><?= money($summary['paid_total']) ?></div>
                <div class="text-muted small"><?= $summary['paid_cnt'] ?> staff</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
            <div class="card-body">
                <div class="text-danger small fw-semibold text-uppercase">Not Paid</div>
                <div class="fs-4 fw-bold text-danger"><?= money($summary['unpaid_total']) ?></div>
                <div class="text-muted small"><?= $summary['unpaid_cnt'] ?> staff</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small fw-semibold text-uppercase">Progress</div>
                <div class="fw-bold mb-2"><?= $pctPaid ?>% paid &mdash; total <?= money($summary['grand_total']) ?></div>
                <div class="progress" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: <?= $pctPaid ?>%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$sections = [
    'Paid'     => ['rows' => $paid,   'badge' => 'success', 'icon' => 'bi-check-circle'],
    'Not Paid' => ['rows' => $unpaid, 'badge' => 'danger',  'icon' => 'bi-hourglass-split'],
];
foreach ($sections as $label => $cfg): if (!$cfg['rows']) continue; ?>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white pt-3">
        <strong><i class="bi <?= $cfg['icon'] ?> text-<?= $cfg['badge'] ?> me-1"></i><?= $label ?></strong>
        <span class="badge bg-<?= $cfg['badge'] ?> ms-2"><?= count($cfg['rows']) ?></span>
    </div>
    <div class="card-body">
        <table id="dataTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr><th>Staff</th><th>Department</th><th>Net Pay</th><th>Status</th><th>Payment Date</th><th>Method</th></tr>
            </thead>
            <tbody>
                <?php $sectionTotal = 0; foreach ($cfg['rows'] as $r): $sectionTotal += (float)$r['NetPay']; ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize(trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? ''))) ?></td>
                    <td><?= sanitize($r['Department'] ?? '') ?></td>
                    <td class="fw-bold"><?= money($r['NetPay']) ?></td>
                    <td><span class="badge bg-<?= $cfg['badge'] ?>"><?= sanitize($r['Status']) ?></span></td>
                    <td><?= sanitize($r['PaymentDate'] ?? '&mdash;') ?></td>
                    <td><?= sanitize($r['PaymentMethod'] ?? '&mdash;') ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="table-light">
                    <td colspan="2" class="fw-bold">Total <?= strtolower($label) ?></td>
                    <td class="fw-bold"><?= money($sectionTotal) ?></td>
                    <td colspan="3"></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php endforeach; ?>

<?php if (!$summary['all_cnt']): ?>
<div class="alert alert-info shadow-sm">No payroll records exist for <?= sanitize(PayrollController::monthName((int)$month)) ?> <?= $year ?> yet.</div>
<?php endif; ?>
