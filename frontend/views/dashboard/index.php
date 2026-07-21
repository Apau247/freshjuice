<?php $pageTitle = 'Dashboard'; ?>
<?php if (!isset($stats)) $stats = []; ?>
<?php if (!isset($recentBatches)) $recentBatches = []; ?>
<?php if (!isset($recentOrders)) $recentOrders = []; ?>

<style>
    .hero-header {
        margin-bottom:1.5rem; padding:2rem; border-radius:var(--radius);
        background:linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
        position:relative; overflow:hidden;
    }
    .hero-header::before {
        content:''; position:absolute; top:-50%; right:-10%; width:400px; height:400px;
        border-radius:50%; background:rgba(34,197,94,0.1); filter:blur(60px);
    }
    .hero-header::after {
        content:''; position:absolute; bottom:-30%; left:20%; width:300px; height:300px;
        border-radius:50%; background:rgba(6,182,212,0.08); filter:blur(50px);
    }
    .hero-header > * { position:relative; z-index:1; }
    .hero-greeting { color:white; font-size:1.4rem; font-weight:800; letter-spacing:-0.03em; margin-bottom:0.2rem; }
    .hero-sub { color:rgba(255,255,255,0.55); font-size:0.82rem; }
    .kpi-card {
        background:var(--glass-bg); backdrop-filter:var(--glass-blur);
        -webkit-backdrop-filter:var(--glass-blur); border:1px solid var(--glass-border);
        border-radius:var(--radius); box-shadow:var(--glass-shadow);
        padding:1.15rem; position:relative; overflow:hidden; transition:all .25s ease; height:100%;
    }
    .kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:var(--radius) var(--radius) 0 0; }
    .kpi-card:hover { transform:translateY(-3px); box-shadow:0 12px 40px rgba(0,0,0,0.1); }
    .kpi-icon {
        width:42px; height:42px; border-radius:11px; display:flex;
        align-items:center; justify-content:center; font-size:1.15rem; color:white; margin-bottom:0.65rem;
    }
    .kpi-value { font-size:1.5rem; font-weight:800; color:#0f172a; letter-spacing:-0.03em; line-height:1; }
    .kpi-label { font-size:0.7rem; font-weight:500; color:#94a3b8; margin-top:0.3rem; text-transform:uppercase; letter-spacing:0.04em; }
    .kpi-card.green::before{background:var(--gradient-brand)}.kpi-card.green .kpi-icon{background:var(--gradient-brand)}
    .kpi-card.blue::before{background:var(--gradient-sky)}.kpi-card.blue .kpi-icon{background:var(--gradient-sky)}
    .kpi-card.amber::before{background:var(--gradient-amber)}.kpi-card.amber .kpi-icon{background:var(--gradient-amber)}
    .kpi-card.rose::before{background:var(--gradient-rose)}.kpi-card.rose .kpi-icon{background:var(--gradient-rose)}
    .kpi-card.violet::before{background:var(--gradient-cool)}.kpi-card.violet .kpi-icon{background:var(--gradient-cool)}
    .kpi-card.warm::before{background:var(--gradient-warm)}.kpi-card.warm .kpi-icon{background:var(--gradient-warm)}
    .chart-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:0.5rem; }
    .chart-title-bar { font-size:0.82rem; font-weight:600; color:#1e293b; display:flex; align-items:center; gap:0.35rem; }
</style>

{{-- Hero Header --}}
<div class="hero-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <?php $h = (int)date('H'); $greet = $h < 12 ? 'Morning' : ($h < 17 ? 'Afternoon' : 'Evening'); ?>
            <h1 class="hero-greeting">Good <?= $greet ?>, <?= sanitize(currentUser()['name']) ?></h1>
            <p class="hero-sub">Here's what's happening at your factory today</p>
        </div>
        <div>
            <button id="btnFullscreen" class="btn btn-sm btn-outline-primary" onclick="toggleDashboardFullscreen()" style="border:1px solid rgba(255,255,255,0.2)!important;background:rgba(255,255,255,0.08)!important;color:white!important;backdrop-filter:blur(10px);">
                <i class="bi bi-arrows-fullscreen me-1"></i> Fullscreen
            </button>
            <button id="btnExitFullscreen" class="btn btn-sm btn-outline-secondary d-none" onclick="toggleDashboardFullscreen()" style="border:1px solid rgba(255,255,255,0.2)!important;background:rgba(255,255,255,0.08)!important;color:white!important;backdrop-filter:blur(10px);">
                <i class="bi bi-fullscreen-exit me-1"></i> Exit
            </button>
        </div>
    </div>
</div>

{{-- KPI Row 1: Operations --}}
<div class="kpi-grid">
    <div class="kpi-card green">
        <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
        <div class="kpi-value"><?= number_format($stats['rawStock'] ?? 0, 1) ?></div>
        <div class="kpi-label">Raw Stock (kg)</div>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-icon"><i class="bi bi-gear-wide-connected"></i></div>
        <div class="kpi-value"><?= $stats['activeBatches'] ?? 0 ?></div>
        <div class="kpi-label">Active Batches</div>
    </div>
    <div class="kpi-card amber">
        <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
        <div class="kpi-value"><?= $stats['pendingOrders'] ?? 0 ?></div>
        <div class="kpi-label">Pending Orders</div>
    </div>
    <div class="kpi-card violet">
        <div class="kpi-icon"><i class="bi bi-cup-straw"></i></div>
        <div class="kpi-value"><?= number_format($stats['totalFG'] ?? 0, 1) ?></div>
        <div class="kpi-label">Finished Goods</div>
    </div>
    <div class="kpi-card rose">
        <div class="kpi-icon"><i class="bi bi-trash3"></i></div>
        <div class="kpi-value"><?= ($stats['wastePct'] ?? 0) ?>%</div>
        <div class="kpi-label">Waste Rate</div>
    </div>
    <div class="kpi-card warm">
        <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="kpi-value">$<?= number_format($stats['totalRevenue'] ?? 0, 0) ?></div>
        <div class="kpi-label">Revenue</div>
    </div>
</div>

{{-- KPI Row 2: Safety & Utilities --}}
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-icon"><i class="bi bi-speedometer2"></i></div>
        <div class="kpi-value"><?= number_format($stats['avgOEE'] ?? 0, 1) ?>%</div>
        <div class="kpi-label">Avg OEE (30d)</div>
    </div>
    <div class="kpi-card rose">
        <div class="kpi-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="kpi-value"><?= $stats['accidentsOpen'] ?? 0 ?></div>
        <div class="kpi-label">Open Incidents</div>
    </div>
    <div class="kpi-card amber">
        <div class="kpi-icon"><i class="bi bi-lightbulb"></i></div>
        <div class="kpi-value"><?= $stats['capaOpen'] ?? 0 ?> / <?= $stats['capaOverdue'] ?? 0 ?></div>
        <div class="kpi-label">CAPA Open / Overdue</div>
    </div>
    <div class="kpi-card green">
        <div class="kpi-icon"><i class="bi bi-droplet"></i></div>
        <div class="kpi-value"><?= number_format($stats['waterTotal'] ?? 0, 0) ?>L</div>
        <div class="kpi-label">Water (Monthly)</div>
    </div>
    <div class="kpi-card violet">
        <div class="kpi-icon"><i class="bi bi-lightning"></i></div>
        <div class="kpi-value"><?= number_format($stats['powerTotal'] ?? 0, 0) ?></div>
        <div class="kpi-label">kWh (Monthly)</div>
    </div>
    <div class="kpi-card warm">
        <div class="kpi-icon"><i class="bi bi-cpu"></i></div>
        <div class="kpi-value"><?= $stats['mDown'] ?? 0 ?> / <?= $stats['mTotal'] ?? 0 ?></div>
        <div class="kpi-label">Machines Down</div>
    </div>
</div>

{{-- Charts --}}
<div class="row g-3 mb-4">
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold"><span class="chart-dot" style="background:var(--gradient-brand);"></span>Production by Flavour</div>
            <div class="card-body"><canvas id="flavourChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold"><span class="chart-dot" style="background:var(--gradient-sky);"></span>Monthly Production</div>
            <div class="card-body"><canvas id="monthlyChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold"><span class="chart-dot" style="background:var(--gradient-rose);"></span>Waste by Type</div>
            <div class="card-body"><canvas id="wasteChart" height="180"></canvas></div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header fw-semibold"><span class="chart-dot" style="background:var(--gradient-cool);"></span>OEE by Machine</div>
            <div class="card-body"><canvas id="oeeChart" height="180"></canvas></div>
        </div>
    </div>
</div>

{{-- Data Tables --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-gear-wide-connected me-2" style="color:#22c55e;"></i>Recent Batches</span>
                <a href="?route=production" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0 no-datatable">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Batch</th><th>Flavour</th><th>Qty</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentBatches as $b): ?>
                        <tr>
                            <td class="fw-semibold"><?= sanitize($b['BatchNumber'] ?? $b['BatchID'] ?? '') ?></td>
                            <td><?= sanitize($b['Flavour'] ?? '') ?></td>
                            <td><?= number_format((float)($b['Quantity'] ?? 0), 1) ?></td>
                            <td>
                                <?php $s = $b['Status'] ?? ''; $map = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Rejected'=>'danger','Cancelled'=>'secondary']; ?>
                                <span class="badge bg-<?= $map[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                            </td>
                            <td><?= sanitize($b['ProductionDate'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; if (empty($recentBatches)): ?><tr><td colspan="5" class="text-center text-muted">No batches yet</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cart3 me-2" style="color:#6366f1;"></i>Recent Orders</span>
                <a href="?route=sales" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0 no-datatable">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Order#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentOrders as $o): ?>
                        <tr>
                            <td class="fw-semibold"><?= sanitize($o['OrderID'] ?? '') ?></td>
                            <td><?= sanitize($o['CustomerName'] ?? '') ?></td>
                            <td>$<?= number_format((float)($o['TotalAmount'] ?? 0), 2) ?></td>
                            <td>
                                <?php $s = $o['Status'] ?? ''; $map = ['Pending'=>'warning','Processing'=>'info','Completed'=>'success','Cancelled'=>'secondary']; ?>
                                <span class="badge bg-<?= $map[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                            </td>
                            <td><?= sanitize($o['OrderDate'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; if (empty($recentOrders)): ?><tr><td colspan="5" class="text-center text-muted">No orders yet</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-shield-check me-2 text-warning"></i>Recent Safety Inspections</span>
                <a href="?route=safety" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0 no-datatable">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Date</th><th>Area</th><th>Type</th><th>Level</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentSafetyInspections ?? [] as $s): ?>
                        <tr>
                            <td><?= sanitize($s['InspectionDate'] ?? '') ?></td>
                            <td><?= sanitize($s['Area'] ?? '') ?></td>
                            <td><?= sanitize($s['InspectionType'] ?? '') ?></td>
                            <td><?php $hl = $s['HazardLevel'] ?? ''; $cmap = ['Low'=>'success','Medium'=>'warning','High'=>'danger','Critical'=>'dark']; ?><span class="badge bg-<?= $cmap[$hl] ?? 'secondary' ?>"><?= sanitize($hl) ?></span></td>
                            <td><?php $st = $s['Status'] ?? ''; $smap = ['Open'=>'danger','In Progress'=>'warning','Closed'=>'success']; ?><span class="badge bg-<?= $smap[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span></td>
                        </tr>
                        <?php endforeach; if (empty($recentSafetyInspections)): ?><tr><td colspan="5" class="text-center text-muted">No inspections yet</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                <span><i class="bi bi-lightbulb me-2 text-info"></i>Open CAPA Initiatives</span>
                <a href="?route=improvement" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0 no-datatable">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Title</th><th>Category</th><th>Target</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($recentImprovements ?? [] as $i): ?>
                        <tr>
                            <td><?= sanitize($i['Title'] ?? '') ?></td>
                            <td><?= sanitize($i['Category'] ?? '') ?></td>
                            <td><?= sanitize($i['TargetDate'] ?? '') ?></td>
                            <td><?php $is = $i['Status'] ?? ''; $imap = ['Proposed'=>'secondary','Approved'=>'primary','In Progress'=>'warning','Completed'=>'success','Cancelled'=>'danger']; ?><span class="badge bg-<?= $imap[$is] ?? 'secondary' ?>"><?= sanitize($is) ?></span></td>
                        </tr>
                        <?php endforeach; if (empty($recentImprovements)): ?><tr><td colspan="4" class="text-center text-muted">No initiatives yet</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDashboardFullscreen() {
    var wrapper = document.getElementById('wrapper');
    var sidebar = document.getElementById('sidebar');
    var btnFull = document.getElementById('btnFullscreen');
    var btnExit = document.getElementById('btnExitFullscreen');
    var navbar = document.querySelector('#page-content-wrapper .navbar');

    wrapper.classList.toggle('dashboard-fullscreen');

    if (wrapper.classList.contains('dashboard-fullscreen')) {
        sidebar.style.display = 'none';
        btnFull.classList.add('d-none');
        btnExit.classList.remove('d-none');
        if (navbar) navbar.style.display = 'none';
        document.querySelector('.container-fluid.p-4').style.padding = '1rem';
    } else {
        sidebar.style.display = '';
        btnFull.classList.remove('d-none');
        btnExit.classList.add('d-none');
        if (navbar) navbar.style.display = '';
        document.querySelector('.container-fluid.p-4').style.padding = '';
    }

    window.dispatchEvent(new Event('resize'));
}

document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($productionByFlavour)): ?>
    new Chart(document.getElementById('flavourChart'), {
        type: 'bar',
        data: {
            labels: [<?php foreach ($productionByFlavour as $f): ?>'<?= addslashes($f['Flavour']) ?>',<?php endforeach; ?>],
            datasets: [{ data: [<?php foreach ($productionByFlavour as $f): ?><?= $f['Total'] ?>,<?php endforeach; ?>], backgroundColor: ['#22c55e','#06b6d4','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'], borderRadius: 8, borderSkipped: false }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } } }
    });
    <?php endif; ?>
    <?php if (!empty($monthlyProduction)): $months = array_reverse($monthlyProduction); ?>
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: [<?php foreach ($months as $m): ?>'<?= $m['Month'] ?>',<?php endforeach; ?>],
            datasets: [{ data: [<?php foreach ($months as $m): ?><?= $m['Total'] ?>,<?php endforeach; ?>], borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#2563eb', borderWidth: 2 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } } }
    });
    <?php endif; ?>
    <?php 
    $wasteByType = $stats['wasteByType'] ?? [];
    if (empty($wasteByType) && !empty($stats['totalWaste'])): 
        $wasteByType = [['WasteType'=>'Production','Total'=>$stats['totalWaste']]];
    endif;
    if (!empty($wasteByType)): 
    ?>
    new Chart(document.getElementById('wasteChart'), {
        type: 'doughnut',
        data: {
            labels: [<?php foreach ($wasteByType as $w): ?>'<?= addslashes($w['WasteType']) ?>',<?php endforeach; ?>],
            datasets: [{ data: [<?php foreach ($wasteByType as $w): ?><?= $w['Total'] ?>,<?php endforeach; ?>], backgroundColor: ['#ef4444','#f97316','#f59e0b','#22c55e','#06b6d4','#8b5cf6'], borderWidth: 0, spacing: 2 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle' } } } }
    });
    <?php endif; ?>
    <?php if (!empty($oeeByMachine)): ?>
    new Chart(document.getElementById('oeeChart'), {
        type: 'bar',
        data: {
            labels: [<?php foreach ($oeeByMachine as $o): ?>'<?= addslashes($o['MachineName']) ?>',<?php endforeach; ?>],
            datasets: [{ data: [<?php foreach ($oeeByMachine as $o): ?><?= number_format((float)$o['AvgOEE'], 1) ?>,<?php endforeach; ?>], backgroundColor: ['#8b5cf6','#6366f1','#a78bfa','#c4b5fd','#ddd6fe','#ede9fe'], borderRadius: 8, borderSkipped: false }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } } }
    });
    <?php endif; ?>
});
</script>
