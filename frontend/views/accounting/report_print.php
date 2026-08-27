<div class="text-center mb-4">
    <h4 class="fw-bold mb-1">FreshJuice Factory</h4>
    <p class="text-muted mb-0">Accounting Report — <?= $monthName ?> <?= $year ?></p>
    <p class="text-muted" style="font-size:.8rem;">Generated: <?= date('d M Y, g:i A') ?></p>
</div>

<div class="row g-3 mb-4">
    <div class="col-4 text-center">
        <div class="border rounded p-2">
            <div class="text-muted" style="font-size:.75rem;">Total Slips</div>
            <div class="fw-bold fs-5"><?= (int)($periodStats['total'] ?? 0) ?></div>
        </div>
    </div>
    <div class="col-4 text-center">
        <div class="border rounded p-2">
            <div class="text-muted" style="font-size:.75rem;">Total Paid</div>
            <div class="fw-bold fs-5 text-success"><?= money((float)($periodStats['paidAmount'] ?? 0)) ?></div>
        </div>
    </div>
    <div class="col-4 text-center">
        <div class="border rounded p-2">
            <div class="text-muted" style="font-size:.75rem;">Total Unpaid</div>
            <div class="fw-bold fs-5 text-warning"><?= money((float)($periodStats['unpaidAmount'] ?? 0)) ?></div>
        </div>
    </div>
</div>

<div class="table-responsive"><table class="table table-bordered" style="font-size:.82rem;">
    <thead class="table-light">
        <tr><th>Staff</th><th>Position</th><th class="text-end">Base</th><th class="text-end">Allow.</th><th class="text-end">Deduct.</th><th class="text-end">Net Pay</th><th>Status</th><th>Method</th><th>Date</th></tr>
    </thead>
    <tbody>
    <?php foreach ($staffSummary as $s): ?>
    <tr>
        <td><?= sanitize($s['FirstName'] . ' ' . $s['LastName']) ?></td>
        <td><?= sanitize($s['Position'] ?? '-') ?></td>
        <td class="text-end"><?= money((float)$s['BaseSalary']) ?></td>
        <td class="text-end"><?= money((float)$s['Allowances']) ?></td>
        <td class="text-end"><?= money((float)$s['Deductions']) ?></td>
        <td class="text-end fw-bold"><?= money((float)$s['NetPay']) ?></td>
        <td><?= $s['Status'] ?></td>
        <td><?= sanitize($s['PaymentMethod'] ?? '-') ?></td>
        <td><?= sanitize($s['PaymentDate'] ?? '-') ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table></div>
