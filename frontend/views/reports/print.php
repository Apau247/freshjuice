<?php
/**
 * Printable report document: company letterhead and title come from the print
 * layout -- this view holds the period, summary figures and results table only.
 */
?>
<div class="text-center text-muted mb-3" style="font-size:0.78rem;">
    <?= sanitize($period ?? '') ?>
</div>

<?php if (!empty($summary)): ?>
<table class="doc-summary">
    <thead><tr><th colspan="2">Summary</th></tr></thead>
    <tbody>
        <?php foreach ($summary as $label => $value): ?>
        <tr>
            <th style="width:45%;"><?= sanitize((string)$label) ?></th>
            <td><?= sanitize((string)$value) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if (empty($rows)): ?>
<p class="text-center my-4">No data for the selected period.</p>
<?php else: ?>
<table class="doc-table">
    <thead>
        <tr><?php foreach ($headers as $h): ?><th><?= sanitize((string)$h) ?></th><?php endforeach; ?></tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr><?php foreach ($row as $cell): ?><td><?= sanitize((string)$cell) ?></td><?php endforeach; ?></tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
