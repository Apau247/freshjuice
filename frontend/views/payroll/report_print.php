<?php /* Payroll paid / not-paid report -- printable document body */ ?>
<p class="text-center text-muted mb-4" style="font-size:0.8rem;">
    Pay period: <strong class="text-dark"><?= sanitize(PayrollController::monthName((int)$month)) ?> <?= (int)$year ?></strong>
</p>

<table class="doc-summary">
    <tr>
        <th>Total Staff</th><td><?= $summary['all_cnt'] ?></td>
        <th>Grand Total</th><td><strong><?= money($summary['grand_total']) ?></strong></td>
    </tr>
    <tr>
        <th>Paid</th><td class="text-success"><strong><?= $summary['paid_cnt'] ?> &middot; <?= money($summary['paid_total']) ?></strong></td>
        <th>Not Paid</th><td class="text-danger"><strong><?= $summary['unpaid_cnt'] ?> &middot; <?= money($summary['unpaid_total']) ?></strong></td>
    </tr>
</table>

<?php
$sections = [
    'A. PAID PAYMENTS'     => ['rows' => $paid,   'total' => $summary['paid_total']],
    'B. NOT-PAID / OUTSTANDING' => ['rows' => $unpaid, 'total' => $summary['unpaid_total']],
];
foreach ($sections as $heading => $cfg): ?>
<h2 style="font-size:0.9rem;font-weight:700;margin:18px 0 8px;"><?= sanitize($heading) ?> (<?= count($cfg['rows']) ?>)</h2>
<?php if ($cfg['rows']): ?>
<table class="doc-table">
    <thead>
        <tr><th style="width:34px">#</th><th>Staff</th><th>Department</th><th>Base Salary</th><th>Allowances</th><th>Deductions</th><th>Net Pay</th><th>Payment Date</th><th>Method</th></tr>
    </thead>
    <tbody>
        <?php foreach ($cfg['rows'] as $i => $r): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= sanitize(trim(($r['FirstName'] ?? '') . ' ' . ($r['LastName'] ?? ''))) ?></td>
            <td><?= sanitize($r['Department'] ?? '') ?></td>
            <td><?= money($r['BaseSalary']) ?></td>
            <td><?= money($r['Allowances']) ?></td>
            <td><?= money($r['Deductions']) ?></td>
            <td><strong><?= money($r['NetPay']) ?></strong></td>
            <td><?= sanitize($r['PaymentDate'] ?? '--') ?></td>
            <td><?= sanitize($r['PaymentMethod'] ?? '--') ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th colspan="6" style="text-align:right;">Total</th>
            <th><?= money($cfg['total']) ?></th>
            <th colspan="2"></th>
        </tr>
    </tbody>
</table>
<?php else: ?>
<p class="text-muted" style="font-size:0.82rem;">No records in this category.</p>
<?php endif; ?>
<?php endforeach; ?>
