<?php $pageTitle = 'Reports'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold mb-0"><i class="bi bi-file-earmark-bar-graph me-2"></i><?= $pageTitle ?></h5>
</div>
<p class="text-muted" style="font-size:0.85rem;">Every report supports a date-range filter and can be exported to CSV or printed / saved as PDF.</p>

<div class="row g-3">
    <?php
    $icons = [
        'production' => 'bi-gear-wide-connected', 'inventory' => 'bi-boxes',
        'sales' => 'bi-cart3', 'supplier' => 'bi-truck', 'waste' => 'bi-trash',
        'water' => 'bi-droplet', 'maintenance' => 'bi-wrench', 'downtime' => 'bi-hourglass-bottom',
        'power' => 'bi-lightning', 'packaging' => 'bi-box', 'certification' => 'bi-award',
        'qaqc' => 'bi-patch-check', 'sop' => 'bi-file-text', 'profit-loss' => 'bi-cash-coin',
        'oee' => 'bi-speedometer2',
    ];
    foreach ($types as $type => $label): ?>
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <a href="?route=reports/view&type=<?= urlencode($type) ?>"
           class="d-flex align-items-center gap-3 text-decoration-none h-100 card border-0 shadow-sm report-card"
           style="border-radius:14px;transition:transform .12s ease, box-shadow .12s ease;">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div style="width:44px;height:44px;border-radius:12px;background:var(--gradient-brand,#22c55e);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="bi <?= $icons[$type] ?? 'bi-table' ?> text-white"></i>
                </div>
                <div>
                    <div class="fw-semibold text-dark" style="font-size:0.9rem;"><?= sanitize($label) ?></div>
                    <div class="text-muted" style="font-size:0.75rem;">Filter &middot; Print &middot; CSV</div>
                </div>
                <i class="bi bi-chevron-right text-muted ms-auto"></i>
            </div>
        </a>
    </div>
    <?php endforeach; ?>
</div>

<style>
    .report-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.10) !important; }
</style>
