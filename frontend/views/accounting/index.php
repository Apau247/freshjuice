<?php $pageTitle = 'Accounting'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-calculator me-2"></i>Accounting — <?= $monthName ?> <?= $year ?></h5>
    <div>
        <a href="?route=accounting/print-report&month=<?= $month ?>&year=<?= $year ?>" class="btn btn-outline-secondary btn-sm me-1" target="_blank"><i class="bi bi-printer me-1"></i>Print Report</a>
        <a href="?route=accounting" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Dashboard</a>
    </div>
</div>

<!-- Period filter -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="route" value="accounting">
            <div class="col-auto">
                <label class="form-label mb-0" style="font-size:.78rem;">Month</label>
                <select name="month" class="form-select form-select-sm">
                    <?php foreach (['January','February','March','April','May','June','July','August','September','October','November','December'] as $i => $m): ?>
                    <option value="<?= $i + 1 ?>" <?= $month === $i + 1 ? 'selected' : '' ?>><?= $m ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <label class="form-label mb-0" style="font-size:.78rem;">Year</label>
                <select name="year" class="form-select form-select-sm">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= $year === $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-auto"><button class="btn btn-primary btn-sm"><i class="bi bi-funnel me-1"></i>Filter</button></div>
        </form>
    </div>
</div>

<!-- Summary cards -->
<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-muted" style="font-size:.78rem;">Total Slips</div>
                <h4 class="fw-bold mb-0 mt-1"><?= (int)($periodStats['total'] ?? 0) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
            <div class="card-body text-center">
                <div class="text-muted" style="font-size:.78rem;">Paid (<?= (int)($periodStats['paid'] ?? 0) ?>)</div>
                <h4 class="fw-bold text-success mb-0 mt-1"><?= money((float)($periodStats['paidAmount'] ?? 0)) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm border-start border-4 border-warning h-100">
            <div class="card-body text-center">
                <div class="text-muted" style="font-size:.78rem;">Unpaid (<?= (int)($periodStats['unpaid'] ?? 0) ?>)</div>
                <h4 class="fw-bold text-warning mb-0 mt-1"><?= money((float)($periodStats['unpaidAmount'] ?? 0)) ?></h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="text-muted" style="font-size:.78rem;">Year-to-Date Paid</div>
                <h4 class="fw-bold text-primary mb-0 mt-1"><?= money((float)($ytd['totalPaid'] ?? 0)) ?></h4>
                <small class="text-muted"><?= (int)($ytd['monthsProcessed'] ?? 0) ?> months processed</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <!-- Allowances / Deductions -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-receipt me-1"></i>Adjustments Summary</h6>
                <div class="table-responsive"><table class="table table-sm mb-0">
                    <tr><td class="text-muted">Total Allowances Paid</td><td class="fw-semibold text-success"><?= money((float)($periodStats['totalAllowances'] ?? 0)) ?></td></tr>
                    <tr><td class="text-muted">Total Deductions Applied</td><td class="fw-semibold text-danger"><?= money((float)($periodStats['totalDeductions'] ?? 0)) ?></td></tr>
                    <tr><td class="text-muted">Net Effect</td><td class="fw-semibold"><?= money((float)($periodStats['totalAllowances'] ?? 0) - (float)($periodStats['totalDeductions'] ?? 0)) ?></td></tr>
                </table></div>
            </div>
        </div>
    </div>
    <!-- Payment Methods -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="fw-bold"><i class="bi bi-wallet2 me-1"></i>Payment Methods</h6>
                <?php if (empty($methodBreakdown)): ?>
                    <p class="text-muted mb-0" style="font-size:.85rem;">No payments recorded this period.</p>
                <?php else: ?>
                <div class="table-responsive"><table class="table table-sm mb-0">
                    <thead><tr><th>Method</th><th class="text-end">Count</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($methodBreakdown as $m): ?>
                    <tr>
                        <td><?= sanitize($m['PaymentMethod']) ?></td>
                        <td class="text-end"><?= (int)$m['cnt'] ?></td>
                        <td class="text-end fw-semibold"><?= money((float)$m['total']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Monthly trend -->
<?php if (!empty($trend)): ?>
<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <h6 class="fw-bold mb-2"><i class="bi bi-graph-up me-1"></i>Monthly Payroll Trend</h6>
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>Period</th><th class="text-end">Paid</th><th class="text-end">Unpaid</th><th class="text-end">Total</th></tr>
                </thead>
                <tbody>
                <?php
                $monthNames = ['', 'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
                foreach ($trend as $t):
                ?>
                <tr>
                    <td><?= $monthNames[(int)$t['PeriodMonth']] ?> <?= $t['PeriodYear'] ?></td>
                    <td class="text-end text-success fw-semibold"><?= money((float)$t['paid']) ?></td>
                    <td class="text-end text-warning"><?= money((float)$t['unpaid']) ?></td>
                    <td class="text-end fw-bold"><?= money((float)$t['paid'] + (float)$t['unpaid']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Per-staff detail table -->
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <h6 class="fw-bold mb-2"><i class="bi bi-people me-1"></i>Staff Payment Detail — <?= $monthName ?> <?= $year ?></h6>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Staff</th><th>Position</th><th class="text-end">Base Salary</th>
                        <th class="text-end">Allowances</th><th class="text-end">Deductions</th>
                        <th class="text-end">Net Pay</th><th>Status</th><th>Method</th><th>Date Paid</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($staffSummary as $s): ?>
                <tr>
                    <td class="fw-semibold"><?= sanitize($s['FirstName'] . ' ' . $s['LastName']) ?></td>
                    <td><?= sanitize($s['Position'] ?? '-') ?></td>
                    <td class="text-end"><?= money((float)$s['BaseSalary']) ?></td>
                    <td class="text-end <?= (float)$s['Allowances'] > 0 ? 'text-success' : '' ?>"><?= money((float)$s['Allowances']) ?></td>
                    <td class="text-end <?= (float)$s['Deductions'] > 0 ? 'text-danger' : '' ?>"><?= money((float)$s['Deductions']) ?></td>
                    <td class="text-end fw-bold"><?= money((float)$s['NetPay']) ?></td>
                    <td>
                        <?php if ($s['Status'] === 'Paid'): ?>
                            <span class="badge bg-success">Paid</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark">Unpaid</span>
                        <?php endif; ?>
                    </td>
                    <td><?= sanitize($s['PaymentMethod'] ?? '-') ?></td>
                    <td><?= sanitize($s['PaymentDate'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php if (empty($staffSummary)): ?>
<div class="alert alert-info mt-3"><i class="bi bi-info-circle me-1"></i>No payroll records for this period.</div>
<?php endif; ?>
