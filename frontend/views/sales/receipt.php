<?php
/**
 * Printable sales receipt: company letterhead and "Sales Receipt" title come
 * from the print layout -- this holds the transaction details only.
 *
 * Supports both single orders ($order) and multi-item POS purchases
 * ($orders, one sales_orders row per cart line).
 */
$orders = $orders ?? [$order];

$totalPayable = 0.0;
$items = [];
foreach ($orders as $o) {
    $qty  = (float)($o['Quantity'] ?? 0);
    $gross = (float)($o['TotalAmount'] ?? 0);
    $totalPayable += $gross;
    $net  = $gross / (1 + 0.15);          // stored amounts are VAT-inclusive
    $vat  = $gross - $net;
    $unit = $qty > 0 ? $net / $qty : 0;
    $label = trim((string)($o['FG_Flavour'] ?? ''));
    if ($label === '') {
        // Strip any POS bookkeeping tags before showing notes as the label.
        $label = trim(preg_replace('/\[[^\]]*\]/', '', (string)($o['Notes'] ?? ''))) ?: 'Sale item';
    }
    $items[] = [
        'id'    => (string)$o['OrderID'],
        'name'  => $label,
        'qty'   => $qty,
        'unit'  => (string)($o['FG_Unit'] ?? ''),
        'unit_price' => $unit,
        'net'   => $net,
        'vat'   => $vat,
        'gross' => $gross,
    ];
}
$subtotal = array_sum(array_column($items, 'net'));
$totalVat = array_sum(array_column($items, 'vat'));
$multi = count($orders) > 1;
?>
<div class="table-responsive"><table class="doc-summary" style="margin-bottom:10px;">
    <tbody>
        <tr>
            <th style="width:30%;">Receipt No</th>
            <td><strong><?= sanitize((string)$orders[0]['OrderID']) ?></strong><?= $multi ? ' <span class="text-muted">+ ' . (count($orders) - 1) . ' more item(s)</span>' : '' ?></td>
        </tr>
        <tr>
            <th>Date</th>
            <td><?= sanitize(date('d M Y, H:i', strtotime((string)$orders[0]['OrderDate']))) ?></td>
        </tr>
        <tr>
            <th>Customer</th>
            <td><?= sanitize((string)($orders[0]['CustomerName'] ?? '-')) ?></td>
        </tr>
        <?php if (!empty($orders[0]['CustomerPhone'])): ?>
        <tr><th>Customer Phone</th><td><?= sanitize((string)$orders[0]['CustomerPhone']) ?></td></tr>
        <?php endif; ?>
        <tr>
            <th>Served By</th>
            <td><?= sanitize((string)($orders[0]['ServedBy'] ?? '-')) ?></td>
        </tr>
        <tr>
            <th>Payment</th>
            <td><?= sanitize((string)($orders[0]['Status'] ?? '-')) ?><?= isset($orders[0]['PaymentStatus']) ? ' &middot; ' . sanitize((string)$orders[0]['PaymentStatus']) : '' ?></td>
        </tr>
    </tbody>
</table></div>

<div class="table-responsive"><table class="doc-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th style="width:14%;">Quantity</th>
            <th style="width:20%;">Unit Price</th>
            <th style="width:22%;">Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $i => $it): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= sanitize($it['name']) ?></td>
            <td><?= number_format($it['qty'], 2) ?> <?= sanitize($it['unit']) ?></td>
            <td><?= money($it['unit_price']) ?></td>
            <td><?= money($it['net']) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4" class="text-end">Subtotal</td>
            <td><?= money($subtotal) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="text-end">VAT (15%)</td>
            <td><?= money($totalVat) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="text-end"><strong>TOTAL PAYABLE</strong></td>
            <td><strong><?= money($totalPayable) ?></strong></td>
        </tr>
    </tbody>
</table></div>

<?php if (!empty($orders[0]['Notes'])): ?>
<p style="font-size:0.8rem;margin-top:10px;"><strong>Notes:</strong> <?= sanitize(preg_replace('/\[Item \d+ of \d+\]\s*/', '', (string)$orders[0]['Notes'])) ?></p>
<?php endif; ?>

<?php if ($multi): ?>
<p style="font-size:0.72rem;margin-top:8px;" class="text-muted">Order IDs: <?= sanitize(implode(', ', array_column($items, 'id'))) ?></p>
<?php endif; ?>

<p class="text-center mt-4" style="font-size:0.85rem;">Thank you for your purchase!</p>

<div class="row mt-4 pt-3" style="font-size:0.75rem;">
    <div class="col-6 text-center">
        <div style="border-top:1px solid #374151;" class="mx-3 pt-1">Seller / Signature</div>
    </div>
    <div class="col-6 text-center">
        <div style="border-top:1px solid #374151;" class="mx-3 pt-1">Accountant / Issuer Stamp &amp; Signature</div>
    </div>
</div>
