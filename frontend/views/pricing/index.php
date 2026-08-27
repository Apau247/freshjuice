<?php
$pageTitle = 'Product Prices';
?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="fw-bold mb-0"><i class="bi bi-tags me-2"></i>Product Prices</h5>
    <?php if ($canEdit): ?>
    <button type="submit" form="priceForm" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i>Save All Prices</button>
    <?php endif; ?>
</div>

<div class="alert alert-info shadow-sm small">
    <i class="bi bi-info-circle me-1"></i>
    Set the default <strong>selling price (GH&#8373; per unit)</strong> for each product flavour. The sales cart pre-fills from these prices.
    <?php if ($canEdit): ?>Prices are managed by the Sales Officer, Factory Manager and Administrator.<?php endif; ?>
</div>

<form method="post" action="?route=prices/save" id="priceForm">
    <?= csrfField() ?>
    <input type="hidden" name="prices" id="pricesJson">
    <?php if (!$catalogue): ?>
    <div class="alert alert-info shadow-sm"><i class="bi bi-info-circle me-2"></i>No products yet &mdash; finished goods appear here automatically once production completes.</div>
    <?php else: ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive"><table id="dataTable" class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>Product (Flavour)</th><th>Unit</th><th>Stock Available</th><th style="width:230px">Selling Price</th><th>Last Updated</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($catalogue as $item): $priced = $item['UnitPrice'] !== null && (float)$item['UnitPrice'] > 0; ?>
                    <tr>
                        <td class="fw-semibold"><?= sanitize($item['Flavour']) ?></td>
                        <td><?= sanitize($item['Unit'] ?? 'bottles') ?></td>
                        <td><?= number_format((float)$item['StockAvailable'], 0) ?></td>
                        <td>
                            <?php if ($canEdit): ?>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">GH&#8373;</span>
                                <input type="number" step="0.01" min="0" max="100000"
                                       class="form-control price-input"
                                       data-flavour="<?= sanitize($item['Flavour']) ?>"
                                       value="<?= number_format((float)($item['UnitPrice'] ?? 0), 2, '.', '') ?>"
                                       placeholder="Not set">
                            </div>
                            <?php else: ?>
                                <?= $priced ? money($item['UnitPrice']) : '<span class="text-muted">Not set</span>' ?>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted">
                            <?= $item['updated_at'] ? sanitize(date('d M Y', strtotime((string)$item['updated_at']))) : '' ?>
                            <?= !empty($item['UpdatedByName']) ? ' &middot; ' . sanitize($item['UpdatedByName']) : '' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table></div>
        </div>
    </div>
    <?php endif; ?>
</form>

<?php if ($canEdit): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('priceForm').addEventListener('submit', function () {
        var out = {};
        document.querySelectorAll('.price-input').forEach(function (input) {
            out[input.dataset.flavour] = input.value;
        });
        document.getElementById('pricesJson').value = JSON.stringify(out);
    });
});
</script>
<?php endif; ?>
