<?php
/**
 * Printable sales receipt: company letterhead and "Sales Receipt" title come
 * from the print layout -- this holds the transaction details only.
 */
$qty    = (float)($order['Quantity'] ?? 0);
$total  = (float)($order['TotalAmount'] ?? 0);
$unit   = $qty > 0 ? $total / $qty : 0;
?>
<table class="doc-summary" style="margin-bottom:10px;">
    <tbody>
        <tr>
            <th style="width:30%;">Receipt No</th>
            <td><strong><?= sanitize((string)$order['OrderID']) ?></strong></td>
        </tr>
        <tr>
            <th>Date</th>
            <td><?= sanitize(date('d M Y, H:i', strtotime((string)$order['OrderDate']))) ?></td>
        </tr>
        <tr>
            <th>Customer</th>
            <td><?= sanitize((string)($order['CustomerName'] ?? '-')) ?></td>
        </tr>
        <?php if (!empty($order['CustomerPhone'])): ?>
        <tr><th>Customer Phone</th><td><?= sanitize((string)$order['CustomerPhone']) ?></td></tr>
        <?php endif; ?>
        <tr>
            <th>Served By</th>
            <td><?= sanitize((string)($order['ServedBy'] ?? '-')) ?></td>
        </tr>
        <tr>
            <th>Payment</th>
            <td><?= sanitize((string)($order['Status'] ?? '-')) ?><?= isset($order['PaymentStatus']) ? ' &middot; ' . sanitize((string)$order['PaymentStatus']) : '' ?></td>
        </tr>
    </tbody>
</table>

<table class="doc-table">
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
        <tr>
            <td>1</td>
            <td><?= sanitize((string)($order['FG_Flavour'] ?? ($order['Notes'] ? 'Custom order' : 'Sale item'))) ?></td>
            <td><?= number_format($qty, 2) ?> <?= sanitize((string)($order['FG_Unit'] ?? '')) ?></td>
            <td><?= number_format($unit, 2) ?></td>
            <td><?= number_format($total, 2) ?></td>
        </tr>
        <tr>
            <td colspan="4" class="text-end"><strong>Total (<?= strtoupper((string)($order['Status'] ?? '')) ?>)</strong></td>
            <td><strong><?= number_format($total, 2) ?></strong></td>
        </tr>
    </tbody>
</table>

<?php if (!empty($order['Notes'])): ?>
<p style="font-size:0.8rem;margin-top:10px;"><strong>Notes:</strong> <?= sanitize((string)$order['Notes']) ?></p>
<?php endif; ?>

<p class="text-center mt-4" style="font-size:0.85rem;">Thank you for your purchase!</p>
