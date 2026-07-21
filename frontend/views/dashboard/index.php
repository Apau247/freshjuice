<?php
$pageTitle = 'Dashboard';

if (!isset($stats)) $stats = [];
if (!isset($recentBatches)) $recentBatches = [];
if (!isset($recentOrders)) $recentOrders = [];
if (!isset($productionByFlavour)) $productionByFlavour = [];
if (!isset($monthlyProduction)) $monthlyProduction = [];
if (!isset($oeeByMachine)) $oeeByMachine = [];
if (!isset($wasteByType)) $wasteByType = [];
if (!isset($recentSafetyInspections)) $recentSafetyInspections = [];
if (!isset($recentImprovements)) $recentImprovements = [];

$greeting = match(true) {
    (int)date('H') < 12  => 'Good Morning',
    (int)date('H') < 17  => 'Good Afternoon',
    default               => 'Good Evening',
};
$today = date('l, F j, Y');
?>

<style>
/* ═══════════════════════════════════════════════════════════
   Dashboard — Scoped Styles
   ═══════════════════════════════════════════════════════════ */
:root {
    --dash-accent: #22c55e;
    --dash-radius: 16px;
    --dash-radius-sm: 10px;
    --dash-glass: rgba(255,255,255,0.78);
    --dash-glass-border: rgba(255,255,255,0.45);
    --dash-glass-blur: blur(20px);
    --dash-shadow: 0 4px 24px rgba(0,0,0,0.05);
    --dash-shadow-lg: 0 12px 40px rgba(0,0,0,0.1);
    --dash-text: #0f172a;
    --dash-text-muted: #94a3b8;
    --dash-surface: #eef2f7;
}

/* ── Hero Header ── */
.dash-hero {
    margin-bottom: 1.75rem;
    padding: 2.25rem 2rem;
    border-radius: var(--dash-radius);
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #0f766e 100%);
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(15, 23, 42, 0.35);
}
.dash-hero::before {
    content: '';
    position: absolute;
    top: -60%;
    right: -8%;
    width: 420px;
    height: 420px;
    border-radius: 50%;
    background: rgba(34,197,94,0.12);
    filter: blur(80px);
    pointer-events: none;
}
.dash-hero::after {
    content: '';
    position: absolute;
    bottom: -40%;
    left: 15%;
    width: 320px;
    height: 320px;
    border-radius: 50%;
    background: rgba(6,182,212,0.08);
    filter: blur(60px);
    pointer-events: none;
}
.dash-hero > * { position: relative; z-index: 1; }

.dash-hero-greeting {
    color: #fff;
    font-size: 1.65rem;
    font-weight: 800;
    letter-spacing: -0.035em;
    margin-bottom: 0.15rem;
    line-height: 1.2;
}
.dash-hero-date {
    color: rgba(255,255,255,0.5);
    font-size: 0.82rem;
    font-weight: 500;
}
.dash-hero-sub {
    color: rgba(255,255,255,0.45);
    font-size: 0.82rem;
    margin-top: 0.15rem;
}
.dash-hero-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.dash-hero-btn {
    border: 1px solid rgba(255,255,255,0.15) !important;
    background: rgba(255,255,255,0.07) !important;
    color: rgba(255,255,255,0.8) !important;
    backdrop-filter: blur(12px);
    border-radius: var(--dash-radius-sm) !important;
    font-size: 0.78rem;
    font-weight: 500;
    padding: 0.4rem 0.85rem;
    transition: all 0.2s ease;
}
.dash-hero-btn:hover {
    background: rgba(255,255,255,0.14) !important;
    color: #fff !important;
    transform: translateY(-1px);
}

/* ── KPI Grid ── */
.dash-kpi-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 1200px) { .dash-kpi-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 768px)  { .dash-kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px)  { .dash-kpi-grid { grid-template-columns: 1fr; } }

.dash-kpi {
    background: var(--dash-glass);
    backdrop-filter: var(--dash-glass-blur);
    -webkit-backdrop-filter: var(--dash-glass-blur);
    border: 1px solid var(--dash-glass-border);
    border-radius: var(--dash-radius);
    box-shadow: var(--dash-shadow);
    padding: 1.1rem 1.15rem;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
}
.dash-kpi::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    border-radius: var(--dash-radius) var(--dash-radius) 0 0;
    background: var(--kpi-accent, var(--dash-accent));
}
.dash-kpi:hover {
    transform: translateY(-5px);
    box-shadow: var(--dash-shadow-lg);
}
.dash-kpi-icon {
    width: 40px;
    height: 40px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    color: #fff;
    background: var(--kpi-accent, var(--dash-accent));
    margin-bottom: 0.7rem;
}
.dash-kpi-value {
    font-size: 1.45rem;
    font-weight: 800;
    color: var(--dash-text);
    letter-spacing: -0.03em;
    line-height: 1;
    margin-bottom: 0.15rem;
}
.dash-kpi-label {
    font-size: 0.68rem;
    font-weight: 600;
    color: var(--dash-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.3rem;
}
.dash-kpi-trend {
    font-size: 0.7rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.1rem 0.4rem;
    border-radius: 6px;
}
.dash-kpi-trend.up { color: #16a34a; background: #dcfce7; }
.dash-kpi-trend.down { color: #dc2626; background: #fee2e2; }
.dash-kpi-trend.neutral { color: #64748b; background: #f1f5f9; }

/* ── Section Headers ── */
.dash-section-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.dash-section-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}
.dash-section-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--dash-text);
    letter-spacing: -0.01em;
}

/* ── Chart Cards ── */
.dash-chart-card {
    background: var(--dash-glass);
    backdrop-filter: var(--dash-glass-blur);
    -webkit-backdrop-filter: var(--dash-glass-blur);
    border: 1px solid var(--dash-glass-border);
    border-radius: var(--dash-radius);
    box-shadow: var(--dash-shadow);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    overflow: hidden;
}
.dash-chart-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--dash-shadow-lg);
}
.dash-chart-card .card-header {
    background: rgba(255,255,255,0.5);
    border-bottom: 1px solid rgba(0,0,0,0.04);
    padding: 0.85rem 1.15rem;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--dash-text);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.dash-chart-card .card-body {
    padding: 1rem 1.15rem;
}

/* ── Data Cards ── */
.dash-data-card {
    background: var(--dash-glass);
    backdrop-filter: var(--dash-glass-blur);
    -webkit-backdrop-filter: var(--dash-glass-blur);
    border: 1px solid var(--dash-glass-border);
    border-radius: var(--dash-radius);
    box-shadow: var(--dash-shadow);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}
.dash-data-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--dash-shadow-lg);
}
.dash-data-card .card-header {
    background: rgba(255,255,255,0.5);
    border-bottom: 1px solid rgba(0,0,0,0.04);
    padding: 0.85rem 1.15rem;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--dash-text);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.dash-data-card .card-body { padding: 0; }

/* ── Data Tables ── */
.dash-table {
    width: 100%;
    border-collapse: collapse;
}
.dash-table thead th {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--dash-text-muted);
    padding: 0.65rem 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    background: rgba(0,0,0,0.015);
    white-space: nowrap;
}
.dash-table tbody td {
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    color: #334155;
    border-bottom: 1px solid rgba(0,0,0,0.03);
    vertical-align: middle;
}
.dash-table tbody tr {
    transition: background 0.15s ease;
}
.dash-table tbody tr:hover td {
    background: rgba(34,197,94,0.025);
}
.dash-table tbody tr:last-child td {
    border-bottom: none;
}
.dash-empty {
    text-align: center;
    padding: 2rem 1rem;
    color: var(--dash-text-muted);
    font-size: 0.82rem;
}
.dash-empty i {
    font-size: 1.5rem;
    display: block;
    margin-bottom: 0.5rem;
    opacity: 0.4;
}

/* ── View All Link ── */
.dash-view-all {
    font-size: 0.72rem;
    font-weight: 600;
    color: #6366f1;
    text-decoration: none;
    padding: 0.25rem 0.65rem;
    border-radius: 6px;
    border: 1px solid rgba(99,102,241,0.2);
    transition: all 0.15s ease;
}
.dash-view-all:hover {
    background: rgba(99,102,241,0.06);
    color: #4f46e5;
    border-color: rgba(99,102,241,0.35);
}

/* ── Status Badges ── */
.dash-badge {
    font-size: 0.68rem;
    font-weight: 600;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    display: inline-block;
    white-space: nowrap;
}
.dash-badge-success { background: #dcfce7; color: #16a34a; }
.dash-badge-warning { background: #fef3c7; color: #d97706; }
.dash-badge-danger  { background: #fee2e2; color: #dc2626; }
.dash-badge-info    { background: #e0f2fe; color: #0284c7; }
.dash-badge-secondary { background: #f1f5f9; color: #64748b; }
.dash-badge-primary { background: #dbeafe; color: #2563eb; }
.dash-badge-dark    { background: #1e293b; color: #fff; }

/* ── Chart Dot Indicators ── */
.dash-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}

/* ── Fullscreen ── */
.dashboard-fullscreen #sidebar { display: none !important; }
.dashboard-fullscreen #page-content-wrapper { margin-left: 0 !important; width: 100% !important; }
.dashboard-fullscreen .navbar { display: none !important; }

/* ── Responsive Tweaks ── */
@media (max-width: 992px) {
    .dash-hero { padding: 1.75rem 1.5rem; }
    .dash-hero-greeting { font-size: 1.35rem; }
}
@media (max-width: 576px) {
    .dash-hero { padding: 1.5rem 1.25rem; }
    .dash-hero-greeting { font-size: 1.15rem; }
    .dash-hero-date { font-size: 0.75rem; }
}
</style>

<!-- ═══════════════════════════════════════════════════════════
     HERO HEADER
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-hero">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="dash-hero-greeting"><?= $greeting ?>, <?= sanitize(currentUser()['name']) ?></h1>
            <div class="dash-hero-date"><i class="bi bi-calendar3 me-1"></i><?= $today ?></div>
            <p class="dash-hero-sub">Here's what's happening across your factory today</p>
        </div>
        <div class="dash-hero-actions">
            <button id="btnFullscreen" class="btn btn-sm dash-hero-btn" onclick="toggleDashboardFullscreen()">
                <i class="bi bi-arrows-fullscreen me-1"></i> Fullscreen
            </button>
            <button id="btnExitFullscreen" class="btn btn-sm dash-hero-btn d-none" onclick="toggleDashboardFullscreen()">
                <i class="bi bi-fullscreen-exit me-1"></i> Exit
            </button>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     KPI ROW 1 — OPERATIONS
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-section-header">
    <span class="dash-dot" style="background:var(--dash-accent);"></span>
    <span class="dash-section-title">Operations Overview</span>
</div>
<div class="dash-kpi-grid">
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #22c55e, #06b6d4);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #22c55e, #06b6d4);"><i class="bi bi-box-seam"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['rawStock'] ?? 0, 1) ?></div>
        <div class="dash-kpi-label">Raw Stock (kg)</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-dash"></i> In stock</div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #0ea5e9, #2563eb);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #0ea5e9, #2563eb);"><i class="bi bi-gear-wide-connected"></i></div>
        <div class="dash-kpi-value"><?= $stats['activeBatches'] ?? 0 ?></div>
        <div class="dash-kpi-label">Active Batches</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-arrow-right"></i> In progress</div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #f59e0b, #d97706);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #f59e0b, #d97706);"><i class="bi bi-clock-history"></i></div>
        <div class="dash-kpi-value"><?= $stats['pendingOrders'] ?? 0 ?></div>
        <div class="dash-kpi-label">Pending Orders</div>
        <div class="dash-kpi-trend <?= ($stats['pendingOrders'] ?? 0) > 5 ? 'down' : 'neutral' ?>">
            <i class="bi bi-<?= ($stats['pendingOrders'] ?? 0) > 5 ? 'exclamation-triangle' : 'arrow-right' ?>"></i>
            <?= ($stats['pendingOrders'] ?? 0) > 5 ? 'High' : 'Normal' ?>
        </div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #8b5cf6, #6366f1);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #8b5cf6, #6366f1);"><i class="bi bi-cup-straw"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['totalFG'] ?? 0, 1) ?></div>
        <div class="dash-kpi-label">Finished Goods</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-box"></i> Units</div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #f43f5e, #e11d48);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #f43f5e, #e11d48);"><i class="bi bi-trash3"></i></div>
        <div class="dash-kpi-value"><?= ($stats['wastePct'] ?? 0) ?>%</div>
        <div class="dash-kpi-label">Waste Rate</div>
        <div class="dash-kpi-trend <?= ($stats['wastePct'] ?? 0) > 10 ? 'down' : (($stats['wastePct'] ?? 0) > 0 ? 'up' : 'neutral') ?>">
            <i class="bi bi-<?= ($stats['wastePct'] ?? 0) > 10 ? 'arrow-up' : 'arrow-down' ?>"></i>
            <?= ($stats['wastePct'] ?? 0) > 10 ? 'Needs attention' : 'Within target' ?>
        </div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #f97316, #ef4444);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #f97316, #ef4444);"><i class="bi bi-currency-dollar"></i></div>
        <div class="dash-kpi-value">$<?= number_format($stats['totalRevenue'] ?? 0, 0) ?></div>
        <div class="dash-kpi-label">Revenue</div>
        <div class="dash-kpi-trend up"><i class="bi bi-arrow-up"></i> Cumulative</div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     KPI ROW 2 — SAFETY & UTILITIES
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-section-header">
    <span class="dash-dot" style="background:linear-gradient(135deg, #0ea5e9, #2563eb);"></span>
    <span class="dash-section-title">Safety, Efficiency & Utilities</span>
</div>
<div class="dash-kpi-grid">
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #0ea5e9, #2563eb);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #0ea5e9, #2563eb);"><i class="bi bi-speedometer2"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['avgOEE'] ?? 0, 1) ?>%</div>
        <div class="dash-kpi-label">Avg OEE (30d)</div>
        <div class="dash-kpi-trend <?= ($stats['avgOEE'] ?? 0) >= 85 ? 'up' : (($stats['avgOEE'] ?? 0) >= 65 ? 'neutral' : 'down') ?>">
            <i class="bi bi-<?= ($stats['avgOEE'] ?? 0) >= 85 ? 'arrow-up' : (($stats['avgOEE'] ?? 0) >= 65 ? 'dash' : 'arrow-down') ?>"></i>
            <?= ($stats['avgOEE'] ?? 0) >= 85 ? 'World class' : (($stats['avgOEE'] ?? 0) >= 65 ? 'Acceptable' : 'Below target') ?>
        </div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #f43f5e, #e11d48);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #f43f5e, #e11d48);"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="dash-kpi-value"><?= $stats['accidentsOpen'] ?? 0 ?></div>
        <div class="dash-kpi-label">Open Incidents</div>
        <div class="dash-kpi-trend <?= ($stats['accidentsOpen'] ?? 0) > 0 ? 'down' : 'up' ?>">
            <i class="bi bi-<?= ($stats['accidentsOpen'] ?? 0) > 0 ? 'exclamation-circle' : 'check-circle' ?>"></i>
            <?= ($stats['accidentsOpen'] ?? 0) > 0 ? 'Action needed' : 'All clear' ?>
        </div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #f59e0b, #d97706);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #f59e0b, #d97706);"><i class="bi bi-lightbulb"></i></div>
        <div class="dash-kpi-value"><?= $stats['capaOpen'] ?? 0 ?> / <?= $stats['capaOverdue'] ?? 0 ?></div>
        <div class="dash-kpi-label">CAPA Open / Overdue</div>
        <div class="dash-kpi-trend <?= ($stats['capaOverdue'] ?? 0) > 0 ? 'down' : 'up' ?>">
            <i class="bi bi-<?= ($stats['capaOverdue'] ?? 0) > 0 ? 'clock-history' : 'check-circle' ?>"></i>
            <?= ($stats['capaOverdue'] ?? 0) > 0 ? 'Overdue items' : 'On track' ?>
        </div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #22c55e, #06b6d4);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #22c55e, #06b6d4);"><i class="bi bi-droplet"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['waterTotal'] ?? 0, 0) ?>L</div>
        <div class="dash-kpi-label">Water (Monthly)</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-droplet-half"></i> Consumption</div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #8b5cf6, #6366f1);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #8b5cf6, #6366f1);"><i class="bi bi-lightning"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['powerTotal'] ?? 0, 0) ?></div>
        <div class="dash-kpi-label">kWh (Monthly)</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-lightning-charge"></i> Consumption</div>
    </div>
    <div class="dash-kpi" style="--kpi-accent: linear-gradient(135deg, #f97316, #ef4444);">
        <div class="dash-kpi-icon" style="background:linear-gradient(135deg, #f97316, #ef4444);"><i class="bi bi-cpu"></i></div>
        <div class="dash-kpi-value"><?= $stats['mDown'] ?? 0 ?> / <?= $stats['mTotal'] ?? 0 ?></div>
        <div class="dash-kpi-label">Machines Down</div>
        <div class="dash-kpi-trend <?= ($stats['mDown'] ?? 0) > 0 ? 'down' : 'up' ?>">
            <i class="bi bi-<?= ($stats['mDown'] ?? 0) > 0 ? 'x-circle' : 'check-circle' ?>"></i>
            <?= ($stats['mDown'] ?? 0) > 0 ? ($stats['mDown'] ?? 0) . ' offline' : 'All operational' ?>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     CHARTS ROW
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-section-header">
    <span class="dash-dot" style="background:linear-gradient(135deg, #8b5cf6, #6366f1);"></span>
    <span class="dash-section-title">Analytics</span>
</div>
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="dash-chart-card">
            <div class="card-header">
                <span class="dash-dot" style="background:linear-gradient(135deg, #22c55e, #4ade80);"></span>
                Production by Flavour
            </div>
            <div class="card-body">
                <?php if (!empty($productionByFlavour)): ?>
                <canvas id="flavourChart" height="200"></canvas>
                <?php else: ?>
                <div class="dash-empty"><i class="bi bi-pie-chart"></i>No production data yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="dash-chart-card">
            <div class="card-header">
                <span class="dash-dot" style="background:linear-gradient(135deg, #0ea5e9, #2563eb);"></span>
                Monthly Production
            </div>
            <div class="card-body">
                <?php if (!empty($monthlyProduction)): ?>
                <canvas id="monthlyChart" height="200"></canvas>
                <?php else: ?>
                <div class="dash-empty"><i class="bi bi-graph-up"></i>No monthly data yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="dash-chart-card">
            <div class="card-header">
                <span class="dash-dot" style="background:linear-gradient(135deg, #f43f5e, #e11d48);"></span>
                Waste by Type
            </div>
            <div class="card-body">
                <?php
                $wasteData = $wasteByType ?? ($stats['wasteByType'] ?? []);
                if (empty($wasteData) && !empty($stats['totalWaste'])):
                    $wasteData = [['WasteType' => 'Production', 'Total' => $stats['totalWaste']]];
                endif;
                ?>
                <?php if (!empty($wasteData)): ?>
                <canvas id="wasteChart" height="200"></canvas>
                <?php else: ?>
                <div class="dash-empty"><i class="bi bi-trash"></i>No waste recorded</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="dash-chart-card">
            <div class="card-header">
                <span class="dash-dot" style="background:linear-gradient(135deg, #8b5cf6, #6366f1);"></span>
                OEE by Machine
            </div>
            <div class="card-body">
                <?php if (!empty($oeeByMachine)): ?>
                <canvas id="oeeChart" height="200"></canvas>
                <?php else: ?>
                <div class="dash-empty"><i class="bi bi-speedometer"></i>No OEE data yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     RECENT DATA TABLES — Row 1
     ═══════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="dash-data-card">
            <div class="card-header">
                <span><i class="bi bi-gear-wide-connected me-2" style="color:#22c55e;"></i>Recent Batches</span>
                <a href="?route=production" class="dash-view-all">View All</a>
            </div>
            <div class="card-body no-datatable">
                <table class="dash-table">
                    <thead>
                        <tr><th>Batch</th><th>Flavour</th><th>Qty</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentBatches)): ?>
                            <?php foreach ($recentBatches as $b): ?>
                            <tr>
                                <td style="font-weight:600;"><?= sanitize($b['BatchNumber'] ?? $b['BatchID'] ?? '') ?></td>
                                <td><?= sanitize($b['Flavour'] ?? '') ?></td>
                                <td><?= number_format((float)($b['Quantity'] ?? 0), 1) ?></td>
                                <td>
                                    <?php
                                    $s = $b['Status'] ?? '';
                                    $bmap = ['Pending' => 'warning', 'In Progress' => 'info', 'Completed' => 'success', 'Rejected' => 'danger', 'Cancelled' => 'secondary'];
                                    ?>
                                    <span class="dash-badge dash-badge-<?= $bmap[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                                </td>
                                <td style="color:var(--dash-text-muted);"><?= sanitize($b['ProductionDate'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5"><div class="dash-empty"><i class="bi bi-gear"></i>No batches yet</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="dash-data-card">
            <div class="card-header">
                <span><i class="bi bi-cart3 me-2" style="color:#6366f1;"></i>Recent Orders</span>
                <a href="?route=sales" class="dash-view-all">View All</a>
            </div>
            <div class="card-body no-datatable">
                <table class="dash-table">
                    <thead>
                        <tr><th>Order#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $o): ?>
                            <tr>
                                <td style="font-weight:600;"><?= sanitize($o['OrderID'] ?? '') ?></td>
                                <td><?= sanitize($o['CustomerName'] ?? '') ?></td>
                                <td>$<?= number_format((float)($o['TotalAmount'] ?? 0), 2) ?></td>
                                <td>
                                    <?php
                                    $s = $o['Status'] ?? '';
                                    $omap = ['Pending' => 'warning', 'Processing' => 'info', 'Completed' => 'success', 'Cancelled' => 'secondary'];
                                    ?>
                                    <span class="dash-badge dash-badge-<?= $omap[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                                </td>
                                <td style="color:var(--dash-text-muted);"><?= sanitize($o['OrderDate'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5"><div class="dash-empty"><i class="bi bi-cart"></i>No orders yet</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     RECENT DATA TABLES — Row 2
     ═══════════════════════════════════════════════════════════ -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="dash-data-card">
            <div class="card-header">
                <span><i class="bi bi-shield-check me-2" style="color:#f59e0b;"></i>Recent Safety Inspections</span>
                <a href="?route=safety" class="dash-view-all">View All</a>
            </div>
            <div class="card-body no-datatable">
                <table class="dash-table">
                    <thead>
                        <tr><th>Date</th><th>Area</th><th>Type</th><th>Level</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentSafetyInspections)): ?>
                            <?php foreach ($recentSafetyInspections as $s): ?>
                            <tr>
                                <td style="color:var(--dash-text-muted);"><?= sanitize($s['InspectionDate'] ?? '') ?></td>
                                <td><?= sanitize($s['Area'] ?? '') ?></td>
                                <td><?= sanitize($s['InspectionType'] ?? '') ?></td>
                                <td>
                                    <?php
                                    $hl = $s['HazardLevel'] ?? '';
                                    $hlmap = ['Low' => 'success', 'Medium' => 'warning', 'High' => 'danger', 'Critical' => 'dark'];
                                    ?>
                                    <span class="dash-badge dash-badge-<?= $hlmap[$hl] ?? 'secondary' ?>"><?= sanitize($hl) ?></span>
                                </td>
                                <td>
                                    <?php
                                    $st = $s['Status'] ?? '';
                                    $smap = ['Open' => 'danger', 'In Progress' => 'warning', 'Closed' => 'success'];
                                    ?>
                                    <span class="dash-badge dash-badge-<?= $smap[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5"><div class="dash-empty"><i class="bi bi-shield-check"></i>No inspections yet</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="dash-data-card">
            <div class="card-header">
                <span><i class="bi bi-lightbulb me-2" style="color:#06b6d4;"></i>Open CAPA Initiatives</span>
                <a href="?route=improvement" class="dash-view-all">View All</a>
            </div>
            <div class="card-body no-datatable">
                <table class="dash-table">
                    <thead>
                        <tr><th>Title</th><th>Category</th><th>Target</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentImprovements)): ?>
                            <?php foreach ($recentImprovements as $i): ?>
                            <tr>
                                <td style="font-weight:600;"><?= sanitize($i['Title'] ?? '') ?></td>
                                <td><?= sanitize($i['Category'] ?? '') ?></td>
                                <td style="color:var(--dash-text-muted);"><?= sanitize($i['TargetDate'] ?? '') ?></td>
                                <td>
                                    <?php
                                    $is = $i['Status'] ?? '';
                                    $imap = ['Proposed' => 'secondary', 'Approved' => 'primary', 'In Progress' => 'warning', 'Completed' => 'success', 'Cancelled' => 'danger'];
                                    ?>
                                    <span class="dash-badge dash-badge-<?= $imap[$is] ?? 'secondary' ?>"><?= sanitize($is) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4"><div class="dash-empty"><i class="bi bi-lightbulb"></i>No initiatives yet</div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════
     JAVASCRIPT
     ═══════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleDashboardFullscreen() {
    var wrapper = document.getElementById('wrapper');
    var btnFull = document.getElementById('btnFullscreen');
    var btnExit = document.getElementById('btnExitFullscreen');
    var navbar = document.querySelector('#page-content-wrapper .navbar');
    var sidebar = document.getElementById('sidebar');

    wrapper.classList.toggle('dashboard-fullscreen');

    if (wrapper.classList.contains('dashboard-fullscreen')) {
        if (sidebar) sidebar.style.display = 'none';
        btnFull.classList.add('d-none');
        btnExit.classList.remove('d-none');
        if (navbar) navbar.style.display = 'none';
        var cf = document.querySelector('.container-fluid.p-4');
        if (cf) cf.style.padding = '1rem';
    } else {
        if (sidebar) sidebar.style.display = '';
        btnFull.classList.remove('d-none');
        btnExit.classList.add('d-none');
        if (navbar) navbar.style.display = '';
        var cf2 = document.querySelector('.container-fluid.p-4');
        if (cf2) cf2.style.padding = '';
    }
    window.dispatchEvent(new Event('resize'));
}

document.addEventListener('DOMContentLoaded', function() {

    /* ── Chart.js Global Defaults ── */
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
        Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;
        Chart.defaults.plugins.legend.labels.padding = 14;
    }

    var chartColors = ['#22c55e','#06b6d4','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'];
    var gridOpts = { color: 'rgba(0,0,0,0.04)' };
    var noGrid = { display: false };

    /* ── Production by Flavour (Bar) ── */
    <?php if (!empty($productionByFlavour)): ?>
    new Chart(document.getElementById('flavourChart'), {
        type: 'bar',
        data: {
            labels: [<?php foreach ($productionByFlavour as $f): ?>'<?= addslashes($f['Flavour']) ?>',<?php endforeach; ?>],
            datasets: [{
                data: [<?php foreach ($productionByFlavour as $f): ?><?= $f['Total'] ?>,<?php endforeach; ?>],
                backgroundColor: chartColors,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 24
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: gridOpts, border: { display: false } },
                x: { grid: noGrid, border: { display: false } }
            }
        }
    });
    <?php endif; ?>

    /* ── Monthly Production (Line) ── */
    <?php if (!empty($monthlyProduction)): $months = array_reverse($monthlyProduction); ?>
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: [<?php foreach ($months as $m): ?>'<?= $m['Month'] ?>',<?php endforeach; ?>],
            datasets: [{
                data: [<?php foreach ($months as $m): ?><?= $m['Total'] ?>,<?php endforeach; ?>],
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.06)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: gridOpts, border: { display: false } },
                x: { grid: noGrid, border: { display: false } }
            }
        }
    });
    <?php endif; ?>

    /* ── Waste by Type (Doughnut) ── */
    <?php if (!empty($wasteData)): ?>
    new Chart(document.getElementById('wasteChart'), {
        type: 'doughnut',
        data: {
            labels: [<?php foreach ($wasteData as $w): ?>'<?= addslashes($w['WasteType']) ?>',<?php endforeach; ?>],
            datasets: [{
                data: [<?php foreach ($wasteData as $w): ?><?= $w['Total'] ?>,<?php endforeach; ?>],
                backgroundColor: ['#ef4444','#f97316','#f59e0b','#22c55e','#06b6d4','#8b5cf6'],
                borderWidth: 0,
                spacing: 3,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 14, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } }
                }
            }
        }
    });
    <?php endif; ?>

    /* ── OEE by Machine (Horizontal Bar) ── */
    <?php if (!empty($oeeByMachine)): ?>
    new Chart(document.getElementById('oeeChart'), {
        type: 'bar',
        data: {
            labels: [<?php foreach ($oeeByMachine as $o): ?>'<?= addslashes($o['MachineName']) ?>',<?php endforeach; ?>],
            datasets: [{
                data: [<?php foreach ($oeeByMachine as $o): ?><?= number_format((float)$o['AvgOEE'], 1) ?>,<?php endforeach; ?>],
                backgroundColor: [<?php foreach ($oeeByMachine as $idx => $o): ?>'rgba(99,102,241,<?= 1 - ($idx * 0.12) ?>)',<?php endforeach; ?>],
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 24
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { beginAtZero: true, max: 100, grid: gridOpts, border: { display: false } },
                y: { grid: noGrid, border: { display: false } }
            }
        }
    });
    <?php endif; ?>
});
</script>
