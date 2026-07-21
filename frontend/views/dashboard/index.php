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
$user = currentUser();
?>

<style>
/* ═══════════════════════════════════════════════════════════
   FreshJuice Dashboard — Scoped Styles
   ═══════════════════════════════════════════════════════════ */
:root {
    --dj-accent: #22c55e;
    --dj-accent-alt: #06b6d4;
    --dj-radius: 16px;
    --dj-radius-sm: 10px;
    --dj-glass: rgba(255,255,255,0.78);
    --dj-glass-border: rgba(255,255,255,0.45);
    --dj-glass-blur: blur(20px);
    --dj-shadow: 0 4px 24px rgba(0,0,0,0.05);
    --dj-shadow-hover: 0 14px 44px rgba(0,0,0,0.1);
    --dj-text: #0f172a;
    --dj-muted: #94a3b8;
    --dj-green: #22c55e;
    --dj-cyan: #06b6d4;
    --dj-blue: #2563eb;
    --dj-indigo: #6366f1;
    --dj-violet: #8b5cf6;
    --dj-amber: #f59e0b;
    --dj-rose: #f43f5e;
    --dj-orange: #f97316;
}

/* ── Hero Header ── */
.dash-hero {
    margin-bottom: 1.75rem;
    padding: 2.5rem 2.25rem;
    border-radius: var(--dj-radius);
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #0f766e 100%);
    position: relative;
    overflow: hidden;
    box-shadow: 0 12px 48px rgba(15, 23, 42, 0.4);
}
.dash-hero::before {
    content: '';
    position: absolute;
    top: -55%;
    right: -5%;
    width: 450px;
    height: 450px;
    border-radius: 50%;
    background: rgba(34,197,94,0.12);
    filter: blur(80px);
    pointer-events: none;
}
.dash-hero::after {
    content: '';
    position: absolute;
    bottom: -35%;
    left: 12%;
    width: 350px;
    height: 350px;
    border-radius: 50%;
    background: rgba(6,182,212,0.09);
    filter: blur(60px);
    pointer-events: none;
}
.dash-hero > * { position: relative; z-index: 1; }

.dash-hero-brand {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-bottom: 0.5rem;
}
.dash-hero-brand svg { flex-shrink: 0; }
.dash-hero-brand-name {
    font-size: 0.9rem;
    font-weight: 700;
    color: rgba(255,255,255,0.6);
    letter-spacing: 0.02em;
    text-transform: uppercase;
}
.dash-hero-greeting {
    color: #fff;
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.04em;
    margin-bottom: 0.2rem;
    line-height: 1.15;
}
.dash-hero-date {
    color: rgba(255,255,255,0.48);
    font-size: 0.82rem;
    font-weight: 500;
}
.dash-hero-sub {
    color: rgba(255,255,255,0.38);
    font-size: 0.82rem;
    margin-top: 0.2rem;
}
.dash-hero-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}
.dash-hero-btn {
    border: 1px solid rgba(255,255,255,0.12) !important;
    background: rgba(255,255,255,0.06) !important;
    color: rgba(255,255,255,0.75) !important;
    backdrop-filter: blur(12px);
    border-radius: var(--dj-radius-sm) !important;
    font-size: 0.76rem;
    font-weight: 500;
    padding: 0.45rem 0.9rem;
    transition: all 0.25s cubic-bezier(.4,0,.2,1);
}
.dash-hero-btn:hover {
    background: rgba(255,255,255,0.13) !important;
    color: #fff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
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
    background: var(--dj-glass);
    backdrop-filter: var(--dj-glass-blur);
    -webkit-backdrop-filter: var(--dj-glass-blur);
    border: 1px solid var(--dj-glass-border);
    border-radius: var(--dj-radius);
    box-shadow: var(--dj-shadow);
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
    border-radius: var(--dj-radius) var(--dj-radius) 0 0;
    background: var(--kpi-c, var(--dj-accent));
}
.dash-kpi:hover {
    transform: translateY(-5px);
    box-shadow: var(--dj-shadow-hover);
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
    background: var(--kpi-c, var(--dj-accent));
    margin-bottom: 0.7rem;
}
.dash-kpi-value {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--dj-text);
    letter-spacing: -0.03em;
    line-height: 1;
    margin-bottom: 0.15rem;
}
.dash-kpi-label {
    font-size: 0.68rem;
    font-weight: 600;
    color: var(--dj-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.3rem;
}
.dash-kpi-trend {
    font-size: 0.68rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.1rem 0.45rem;
    border-radius: 6px;
    line-height: 1;
}
.dash-kpi-trend.up      { color: #16a34a; background: #dcfce7; }
.dash-kpi-trend.down    { color: #dc2626; background: #fee2e2; }
.dash-kpi-trend.neutral { color: #64748b; background: #f1f5f9; }
.dash-kpi-trend.warn    { color: #d97706; background: #fef3c7; }

/* ── Section Headers ── */
.dash-section-hdr {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.dash-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    flex-shrink: 0;
}
.dash-section-title {
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--dj-text);
    letter-spacing: -0.01em;
}

/* ── Chart Cards ── */
.dash-chart-card {
    background: var(--dj-glass);
    backdrop-filter: var(--dj-glass-blur);
    -webkit-backdrop-filter: var(--dj-glass-blur);
    border: 1px solid var(--dj-glass-border);
    border-radius: var(--dj-radius);
    box-shadow: var(--dj-shadow);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    overflow: hidden;
}
.dash-chart-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--dj-shadow-hover);
}
.dash-chart-card .card-header {
    background: rgba(255,255,255,0.5);
    border-bottom: 1px solid rgba(0,0,0,0.04);
    padding: 0.85rem 1.15rem;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--dj-text);
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.dash-chart-card .card-body { padding: 1rem 1.15rem; }

/* ── Data Cards ── */
.dash-data-card {
    background: var(--dj-glass);
    backdrop-filter: var(--dj-glass-blur);
    -webkit-backdrop-filter: var(--dj-glass-blur);
    border: 1px solid var(--dj-glass-border);
    border-radius: var(--dj-radius);
    box-shadow: var(--dj-shadow);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}
.dash-data-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--dj-shadow-hover);
}
.dash-data-card .card-header {
    background: rgba(255,255,255,0.5);
    border-bottom: 1px solid rgba(0,0,0,0.04);
    padding: 0.85rem 1.15rem;
    font-size: 0.8rem;
    font-weight: 700;
    color: var(--dj-text);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.dash-data-card .card-body { padding: 0; }

/* ── Data Tables ── */
.dash-tbl { width: 100%; border-collapse: collapse; }
.dash-tbl thead th {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--dj-muted);
    padding: 0.65rem 1rem;
    border-bottom: 1px solid rgba(0,0,0,0.05);
    background: rgba(0,0,0,0.015);
    white-space: nowrap;
}
.dash-tbl tbody td {
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    color: #334155;
    border-bottom: 1px solid rgba(0,0,0,0.03);
    vertical-align: middle;
}
.dash-tbl tbody tr { transition: background 0.15s ease; }
.dash-tbl tbody tr:hover td { background: rgba(34,197,94,0.025); }
.dash-tbl tbody tr:last-child td { border-bottom: none; }

/* ── Empty States ── */
.dash-empty {
    text-align: center;
    padding: 2.25rem 1rem;
    color: var(--dj-muted);
    font-size: 0.82rem;
}
.dash-empty i {
    font-size: 1.6rem;
    display: block;
    margin-bottom: 0.6rem;
    opacity: 0.35;
}

/* ── View All Link ── */
.dash-view-all {
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--dj-indigo);
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
.dash-badge-success    { background: #dcfce7; color: #16a34a; }
.dash-badge-warning    { background: #fef3c7; color: #d97706; }
.dash-badge-danger     { background: #fee2e2; color: #dc2626; }
.dash-badge-info       { background: #e0f2fe; color: #0284c7; }
.dash-badge-secondary  { background: #f1f5f9; color: #64748b; }
.dash-badge-primary    { background: #dbeafe; color: #2563eb; }
.dash-badge-dark       { background: #1e293b; color: #fff; }

/* ── Fullscreen ── */
.dashboard-fullscreen #sidebar { display: none !important; }
.dashboard-fullscreen #page-content-wrapper { margin-left: 0 !important; width: 100% !important; }
.dashboard-fullscreen .navbar { display: none !important; }

/* ── Responsive ── */
@media (max-width: 992px) {
    .dash-hero { padding: 2rem 1.5rem; }
    .dash-hero-greeting { font-size: 1.4rem; }
}
@media (max-width: 576px) {
    .dash-hero { padding: 1.75rem 1.25rem; }
    .dash-hero-greeting { font-size: 1.2rem; }
    .dash-hero-date { font-size: 0.75rem; }
}
</style>


<!-- ═══════════════════════════════════════════════════════════
     HERO HEADER
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-hero">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <div class="dash-hero-brand">
                <svg width="36" height="36" viewBox="0 0 32 32" fill="none">
                    <rect width="32" height="32" rx="10" fill="url(#hero-grad)"/>
                    <path d="M16 6C11 6 8 10 8 15C8 20 11 26 16 26C21 26 24 20 24 15C24 10 21 6 16 6Z" fill="white" opacity="0.9"/>
                    <path d="M13 14C13 14 14.5 18 16 18C17.5 18 19 14 19 14" stroke="url(#hero-grad)" stroke-width="1.5" stroke-linecap="round"/>
                    <defs>
                        <linearGradient id="hero-grad" x1="0" y1="0" x2="32" y2="32">
                            <stop stop-color="#22c55e"/>
                            <stop offset="1" stop-color="#06b6d4"/>
                        </linearGradient>
                    </defs>
                </svg>
                <span class="dash-hero-brand-name">FreshJuice Factory</span>
            </div>
            <h1 class="dash-hero-greeting"><?= $greeting ?>, <?= sanitize($user['name']) ?></h1>
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
     KPI ROW 1 — SUPPLY CHAIN & PRODUCTION (Modules 1–6)
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-section-hdr">
    <span class="dash-dot" style="background:var(--dj-green);"></span>
    <span class="dash-section-title">Supply Chain & Production</span>
    <span style="font-size:0.65rem;color:var(--dj-muted);margin-left:0.3rem;">Modules 1–6</span>
</div>
<div class="dash-kpi-grid">
    <!-- M1: Supplier Management -->
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#22c55e,#16a34a);">
        <div class="dash-kpi-icon"><i class="bi bi-truck"></i></div>
        <div class="dash-kpi-value"><?= $stats['totalSuppliers'] ?? 0 ?></div>
        <div class="dash-kpi-label">Total Suppliers</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-people"></i> Registered</div>
    </div>
    <!-- M2: Raw Material Inventory -->
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#22c55e,#06b6d4);">
        <div class="dash-kpi-icon"><i class="bi bi-box-seam"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['rawStock'] ?? 0, 1) ?></div>
        <div class="dash-kpi-label">Raw Stock (kg)</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-database"></i> <?= $stats['rmCount'] ?? 0 ?> items</div>
    </div>
    <!-- M3: Packaging Inventory -->
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#06b6d4,#0891b2);">
        <div class="dash-kpi-icon"><i class="bi bi-archive"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['pkgStock'] ?? 0, 0) ?></div>
        <div class="dash-kpi-label">Packaging Stock</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-boxes"></i> Units</div>
    </div>
    <!-- M4: Production Management -->
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#2563eb,#1d4ed8);">
        <div class="dash-kpi-icon"><i class="bi bi-gear-wide-connected"></i></div>
        <div class="dash-kpi-value"><?= $stats['activeBatches'] ?? 0 ?></div>
        <div class="dash-kpi-label">Active Batches</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-arrow-right"></i> In progress</div>
    </div>
    <!-- M5: QA/QC -->
    <?php $pendingQI = $stats['pendingQI'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#f59e0b,#d97706);">
        <div class="dash-kpi-icon"><i class="bi bi-clipboard-check"></i></div>
        <div class="dash-kpi-value"><?= $pendingQI ?></div>
        <div class="dash-kpi-label">Pending QC</div>
        <div class="dash-kpi-trend <?= $pendingQI > 0 ? 'warn' : 'up' ?>">
            <i class="bi bi-<?= $pendingQI > 0 ? 'clock' : 'check-circle' ?>"></i>
            <?= $pendingQI > 0 ? 'Awaiting review' : 'All clear' ?>
        </div>
    </div>
    <!-- M6: Finished Goods -->
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#8b5cf6,#6366f1);">
        <div class="dash-kpi-icon"><i class="bi bi-cup-straw"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['totalFG'] ?? 0, 1) ?></div>
        <div class="dash-kpi-label">Finished Goods</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-box"></i> Units</div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════
     KPI ROW 2 — SALES, WORKFORCE & COMPLIANCE (Modules 7–9, 13–14)
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-section-hdr">
    <span class="dash-dot" style="background:linear-gradient(135deg,#0ea5e9,#2563eb);"></span>
    <span class="dash-section-title">Sales, Workforce & Compliance</span>
    <span style="font-size:0.65rem;color:var(--dj-muted);margin-left:0.3rem;">Modules 7–9, 13–14</span>
</div>
<div class="dash-kpi-grid">
    <!-- M7: Sales & Invoicing — Pending Orders -->
    <?php $pendingCount = $stats['pendingOrders'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#f59e0b,#d97706);">
        <div class="dash-kpi-icon"><i class="bi bi-clock-history"></i></div>
        <div class="dash-kpi-value"><?= $pendingCount ?></div>
        <div class="dash-kpi-label">Pending Orders</div>
        <div class="dash-kpi-trend <?= $pendingCount > 5 ? 'down' : ($pendingCount > 0 ? 'warn' : 'up') ?>">
            <i class="bi bi-<?= $pendingCount > 5 ? 'exclamation-triangle' : ($pendingCount > 0 ? 'clock' : 'check-circle') ?>"></i>
            <?= $pendingCount > 5 ? 'High volume' : ($pendingCount > 0 ? 'Awaiting' : 'All clear') ?>
        </div>
    </div>
    <!-- M7: Sales & Invoicing — Revenue -->
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#f97316,#ea580c);">
        <div class="dash-kpi-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="dash-kpi-value">$<?= number_format($stats['totalRevenue'] ?? 0, 0) ?></div>
        <div class="dash-kpi-label">Revenue</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-graph-up"></i> Cumulative</div>
    </div>
    <!-- M8: Staff & Shift — Training -->
    <?php $trainingPend = $stats['trainingPending'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#8b5cf6,#7c3aed);">
        <div class="dash-kpi-icon"><i class="bi bi-person-video3"></i></div>
        <div class="dash-kpi-value"><?= $trainingPend ?></div>
        <div class="dash-kpi-label">Training Scheduled</div>
        <div class="dash-kpi-trend <?= $trainingPend > 0 ? 'warn' : 'up' ?>">
            <i class="bi bi-<?= $trainingPend > 0 ? 'clock' : 'check-circle' ?>"></i>
            <?= $trainingPend > 0 ? 'Upcoming sessions' : 'None pending' ?>
        </div>
    </div>
    <!-- M9: Maintenance — Machines Down -->
    <?php $mDown = $stats['mDown'] ?? 0; $mTotal = $stats['mTotal'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#f97316,#ef4444);">
        <div class="dash-kpi-icon"><i class="bi bi-cpu"></i></div>
        <div class="dash-kpi-value"><?= $mDown ?> / <?= $mTotal ?></div>
        <div class="dash-kpi-label">Machines Down</div>
        <div class="dash-kpi-trend <?= $mDown > 0 ? 'down' : 'up' ?>">
            <i class="bi bi-<?= $mDown > 0 ? 'x-circle' : 'check-circle' ?>"></i>
            <?= $mDown > 0 ? $mDown . ' offline' : 'All operational' ?>
        </div>
    </div>
    <!-- M13: Certification Management -->
    <?php $certsExpiring = $stats['certsExpiring'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#f43f5e,#e11d48);">
        <div class="dash-kpi-icon"><i class="bi bi-award"></i></div>
        <div class="dash-kpi-value"><?= $certsExpiring ?></div>
        <div class="dash-kpi-label">Certs Expiring (90d)</div>
        <div class="dash-kpi-trend <?= $certsExpiring > 0 ? 'down' : 'up' ?>">
            <i class="bi bi-<?= $certsExpiring > 0 ? 'exclamation-triangle' : 'check-circle' ?>"></i>
            <?= $certsExpiring > 0 ? 'Renew soon' : 'All valid' ?>
        </div>
    </div>
    <!-- M14: SOP Checklists -->
    <?php $sopPend = $stats['sopPending'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#6366f1,#4f46e5);">
        <div class="dash-kpi-icon"><i class="bi bi-list-check"></i></div>
        <div class="dash-kpi-value"><?= $sopPend ?></div>
        <div class="dash-kpi-label">SOP Pending Review</div>
        <div class="dash-kpi-trend <?= $sopPend > 0 ? 'warn' : 'up' ?>">
            <i class="bi bi-<?= $sopPend > 0 ? 'clock' : 'check-circle' ?>"></i>
            <?= $sopPend > 0 ? 'Awaiting approval' : 'All approved' ?>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════
     KPI ROW 3 — UTILITIES, SAFETY & EFFICIENCY (Modules 4, 10–12 + Safety)
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-section-hdr">
    <span class="dash-dot" style="background:linear-gradient(135deg,#f43f5e,#e11d48);"></span>
    <span class="dash-section-title">Utilities, Safety & Efficiency</span>
    <span style="font-size:0.65rem;color:var(--dj-muted);margin-left:0.3rem;">Modules 10–12 + Safety</span>
</div>
<div class="dash-kpi-grid">
    <!-- M4: Production Efficiency (OEE) -->
    <?php $avgOEE = $stats['avgOEE'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#0ea5e9,#2563eb);">
        <div class="dash-kpi-icon"><i class="bi bi-speedometer2"></i></div>
        <div class="dash-kpi-value"><?= number_format($avgOEE, 1) ?>%</div>
        <div class="dash-kpi-label">Avg OEE (30d)</div>
        <div class="dash-kpi-trend <?= $avgOEE >= 85 ? 'up' : ($avgOEE >= 65 ? 'neutral' : 'down') ?>">
            <i class="bi bi-<?= $avgOEE >= 85 ? 'arrow-up' : ($avgOEE >= 65 ? 'dash' : 'arrow-down') ?>"></i>
            <?= $avgOEE >= 85 ? 'World class' : ($avgOEE >= 65 ? 'Acceptable' : 'Below target') ?>
        </div>
    </div>
    <!-- M10: Waste Management -->
    <?php $wastePct = $stats['wastePct'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#f43f5e,#e11d48);">
        <div class="dash-kpi-icon"><i class="bi bi-trash3"></i></div>
        <div class="dash-kpi-value"><?= $wastePct ?>%</div>
        <div class="dash-kpi-label">Waste Rate</div>
        <div class="dash-kpi-trend <?= $wastePct > 10 ? 'down' : ($wastePct > 0 ? 'up' : 'neutral') ?>">
            <i class="bi bi-<?= $wastePct > 10 ? 'arrow-up' : 'arrow-down' ?>"></i>
            <?= $wastePct > 10 ? 'Needs attention' : ($wastePct > 0 ? 'Within target' : 'No waste') ?>
        </div>
    </div>
    <!-- M11: Water Management -->
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#22c55e,#06b6d4);">
        <div class="dash-kpi-icon"><i class="bi bi-droplet"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['waterTotal'] ?? 0, 0) ?>L</div>
        <div class="dash-kpi-label">Water (Monthly)</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-droplet-half"></i> Consumption</div>
    </div>
    <!-- M12: Power Management -->
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#8b5cf6,#6366f1);">
        <div class="dash-kpi-icon"><i class="bi bi-lightning"></i></div>
        <div class="dash-kpi-value"><?= number_format($stats['powerTotal'] ?? 0, 0) ?></div>
        <div class="dash-kpi-label">kWh (Monthly)</div>
        <div class="dash-kpi-trend neutral"><i class="bi bi-lightning-charge"></i> Consumption</div>
    </div>
    <!-- Safety: Open Incidents -->
    <?php $incidents = $stats['accidentsOpen'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#ef4444,#dc2626);">
        <div class="dash-kpi-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="dash-kpi-value"><?= $incidents ?></div>
        <div class="dash-kpi-label">Open Incidents</div>
        <div class="dash-kpi-trend <?= $incidents > 0 ? 'down' : 'up' ?>">
            <i class="bi bi-<?= $incidents > 0 ? 'exclamation-circle' : 'check-circle' ?>"></i>
            <?= $incidents > 0 ? 'Action needed' : 'All clear' ?>
        </div>
    </div>
    <!-- CAPA Initiatives -->
    <?php $capaOpen = $stats['capaOpen'] ?? 0; $capaOverdue = $stats['capaOverdue'] ?? 0; ?>
    <div class="dash-kpi" style="--kpi-c:linear-gradient(135deg,#f59e0b,#d97706);">
        <div class="dash-kpi-icon"><i class="bi bi-lightbulb"></i></div>
        <div class="dash-kpi-value"><?= $capaOpen ?> / <?= $capaOverdue ?></div>
        <div class="dash-kpi-label">CAPA Open / Overdue</div>
        <div class="dash-kpi-trend <?= $capaOverdue > 0 ? 'down' : 'up' ?>">
            <i class="bi bi-<?= $capaOverdue > 0 ? 'clock-history' : 'check-circle' ?>"></i>
            <?= $capaOverdue > 0 ? 'Overdue items' : 'On track' ?>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════
     CHARTS ROW
     ═══════════════════════════════════════════════════════════ -->
<div class="dash-section-hdr">
    <span class="dash-dot" style="background:linear-gradient(135deg,#8b5cf6,#6366f1);"></span>
    <span class="dash-section-title">Analytics</span>
</div>
<div class="row g-3 mb-4">
    <!-- Production by Flavour -->
    <div class="col-lg-3 col-md-6">
        <div class="dash-chart-card">
            <div class="card-header">
                <span class="dash-dot" style="background:linear-gradient(135deg,#22c55e,#4ade80);"></span>
                Production by Flavour
            </div>
            <div class="card-body">
                <?php if (!empty($productionByFlavour)): ?>
                <canvas id="flavourChart" height="220"></canvas>
                <?php else: ?>
                <div class="dash-empty"><i class="bi bi-pie-chart"></i>No production data yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Monthly Production -->
    <div class="col-lg-3 col-md-6">
        <div class="dash-chart-card">
            <div class="card-header">
                <span class="dash-dot" style="background:linear-gradient(135deg,#0ea5e9,#2563eb);"></span>
                Monthly Production
            </div>
            <div class="card-body">
                <?php if (!empty($monthlyProduction)): ?>
                <canvas id="monthlyChart" height="220"></canvas>
                <?php else: ?>
                <div class="dash-empty"><i class="bi bi-graph-up"></i>No monthly data yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Waste by Type -->
    <div class="col-lg-3 col-md-6">
        <div class="dash-chart-card">
            <div class="card-header">
                <span class="dash-dot" style="background:linear-gradient(135deg,#f43f5e,#e11d48);"></span>
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
                <canvas id="wasteChart" height="220"></canvas>
                <?php else: ?>
                <div class="dash-empty"><i class="bi bi-trash"></i>No waste recorded</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- OEE by Machine -->
    <div class="col-lg-3 col-md-6">
        <div class="dash-chart-card">
            <div class="card-header">
                <span class="dash-dot" style="background:linear-gradient(135deg,#8b5cf6,#6366f1);"></span>
                OEE by Machine
            </div>
            <div class="card-body">
                <?php if (!empty($oeeByMachine)): ?>
                <canvas id="oeeChart" height="220"></canvas>
                <?php else: ?>
                <div class="dash-empty"><i class="bi bi-speedometer"></i>No OEE data yet</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════════════════════════
     RECENT TABLES — Row 1
     ═══════════════════════════════════════════════════════════ -->
<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="dash-data-card">
            <div class="card-header">
                <span><i class="bi bi-gear-wide-connected me-2" style="color:#22c55e;"></i>Recent Batches</span>
                <a href="?route=production" class="dash-view-all">View All</a>
            </div>
            <div class="card-body no-datatable">
                <table class="dash-tbl">
                    <thead><tr><th>Batch</th><th>Flavour</th><th>Qty</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php if (!empty($recentBatches)): ?>
                            <?php foreach ($recentBatches as $b): ?>
                            <tr>
                                <td style="font-weight:600;"><?= sanitize($b['BatchNumber'] ?? $b['BatchID'] ?? '') ?></td>
                                <td><?= sanitize($b['Flavour'] ?? '') ?></td>
                                <td><?= number_format((float)($b['Quantity'] ?? 0), 1) ?></td>
                                <td>
                                    <?php $s = $b['Status'] ?? ''; $bm = ['Pending'=>'warning','In Progress'=>'info','Completed'=>'success','Rejected'=>'danger','Cancelled'=>'secondary']; ?>
                                    <span class="dash-badge dash-badge-<?= $bm[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                                </td>
                                <td style="color:var(--dj-muted);"><?= sanitize($b['ProductionDate'] ?? '') ?></td>
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
                <table class="dash-tbl">
                    <thead><tr><th>Order#</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php if (!empty($recentOrders)): ?>
                            <?php foreach ($recentOrders as $o): ?>
                            <tr>
                                <td style="font-weight:600;"><?= sanitize($o['OrderID'] ?? '') ?></td>
                                <td><?= sanitize($o['CustomerName'] ?? '') ?></td>
                                <td>$<?= number_format((float)($o['TotalAmount'] ?? 0), 2) ?></td>
                                <td>
                                    <?php $s = $o['Status'] ?? ''; $om = ['Pending'=>'warning','Processing'=>'info','Completed'=>'success','Cancelled'=>'secondary']; ?>
                                    <span class="dash-badge dash-badge-<?= $om[$s] ?? 'secondary' ?>"><?= sanitize($s) ?></span>
                                </td>
                                <td style="color:var(--dj-muted);"><?= sanitize($o['OrderDate'] ?? '') ?></td>
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
     RECENT TABLES — Row 2
     ═══════════════════════════════════════════════════════════ -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="dash-data-card">
            <div class="card-header">
                <span><i class="bi bi-shield-check me-2" style="color:#f59e0b;"></i>Recent Safety Inspections</span>
                <a href="?route=safety" class="dash-view-all">View All</a>
            </div>
            <div class="card-body no-datatable">
                <table class="dash-tbl">
                    <thead><tr><th>Date</th><th>Area</th><th>Type</th><th>Level</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (!empty($recentSafetyInspections)): ?>
                            <?php foreach ($recentSafetyInspections as $si): ?>
                            <tr>
                                <td style="color:var(--dj-muted);"><?= sanitize($si['InspectionDate'] ?? '') ?></td>
                                <td><?= sanitize($si['Area'] ?? '') ?></td>
                                <td><?= sanitize($si['InspectionType'] ?? '') ?></td>
                                <td>
                                    <?php $hl = $si['HazardLevel'] ?? ''; $hm = ['Low'=>'success','Medium'=>'warning','High'=>'danger','Critical'=>'dark']; ?>
                                    <span class="dash-badge dash-badge-<?= $hm[$hl] ?? 'secondary' ?>"><?= sanitize($hl) ?></span>
                                </td>
                                <td>
                                    <?php $st = $si['Status'] ?? ''; $sm = ['Open'=>'danger','In Progress'=>'warning','Closed'=>'success']; ?>
                                    <span class="dash-badge dash-badge-<?= $sm[$st] ?? 'secondary' ?>"><?= sanitize($st) ?></span>
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
                <table class="dash-tbl">
                    <thead><tr><th>Title</th><th>Category</th><th>Target</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php if (!empty($recentImprovements)): ?>
                            <?php foreach ($recentImprovements as $imp): ?>
                            <tr>
                                <td style="font-weight:600;"><?= sanitize($imp['Title'] ?? '') ?></td>
                                <td><?= sanitize($imp['Category'] ?? '') ?></td>
                                <td style="color:var(--dj-muted);"><?= sanitize($imp['TargetDate'] ?? '') ?></td>
                                <td>
                                    <?php $is = $imp['Status'] ?? ''; $im = ['Proposed'=>'secondary','Approved'=>'primary','In Progress'=>'warning','Completed'=>'success','Cancelled'=>'danger']; ?>
                                    <span class="dash-badge dash-badge-<?= $im[$is] ?? 'secondary' ?>"><?= sanitize($is) ?></span>
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
     JAVASCRIPT — Charts & Interactions
     ═══════════════════════════════════════════════════════════ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* ── Fullscreen Toggle ── */
function toggleDashboardFullscreen() {
    var wrapper  = document.getElementById('wrapper');
    var btnFull  = document.getElementById('btnFullscreen');
    var btnExit  = document.getElementById('btnExitFullscreen');
    var navbar   = document.querySelector('#page-content-wrapper .navbar');
    var sidebar  = document.getElementById('sidebar');

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

/* ── Charts ── */
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    /* Global defaults */
    Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.pointStyleWidth = 8;
    Chart.defaults.plugins.legend.labels.padding = 14;
    Chart.defaults.animation.duration = 800;
    Chart.defaults.animation.easing = 'easeOutQuart';

    var colors = ['#22c55e','#06b6d4','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'];
    var grid   = { color: 'rgba(0,0,0,0.04)' };
    var noGrid = { display: false };

    /* ── Production by Flavour (Bar) ── */
    <?php if (!empty($productionByFlavour)): ?>
    new Chart(document.getElementById('flavourChart'), {
        type: 'bar',
        data: {
            labels: [<?php foreach ($productionByFlavour as $f): ?>'<?= addslashes($f['Flavour']) ?>',<?php endforeach; ?>],
            datasets: [{
                data: [<?php foreach ($productionByFlavour as $f): ?><?= $f['Total'] ?>,<?php endforeach; ?>],
                backgroundColor: colors,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 26
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', titleFont: { weight: 600 }, padding: 10, cornerRadius: 8 } },
            scales: {
                y: { beginAtZero: true, grid: grid, border: { display: false }, ticks: { font: { size: 11 } } },
                x: { grid: noGrid, border: { display: false }, ticks: { font: { size: 11 } } }
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
                pointHoverRadius: 6,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                borderWidth: 2.5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 } },
            scales: {
                y: { beginAtZero: true, grid: grid, border: { display: false }, ticks: { font: { size: 11 } } },
                x: { grid: noGrid, border: { display: false }, ticks: { font: { size: 11 } } }
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
                legend: { position: 'bottom', labels: { padding: 14, usePointStyle: true, pointStyle: 'circle', font: { size: 11 } } },
                tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8 }
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
                backgroundColor: [<?php foreach ($oeeByMachine as $i => $o): ?>'rgba(99,102,241,<?= round(1 - ($i * 0.12), 2) ?>)',<?php endforeach; ?>],
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 26
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0f172a', padding: 10, cornerRadius: 8, callbacks: { label: function(ctx) { return ctx.parsed.x + '%'; } } } },
            scales: {
                x: { beginAtZero: true, max: 100, grid: grid, border: { display: false }, ticks: { font: { size: 11 }, callback: function(v) { return v + '%'; } } },
                y: { grid: noGrid, border: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
    <?php endif; ?>
});
</script>
