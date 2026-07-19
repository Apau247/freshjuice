@extends('layouts.app')
@section('content')
{{-- Hero Header --}}
<div class="hero-header">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="hero-greeting">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ session('user_name', 'User') }}</h1>
            <p class="hero-sub">Here's what's happening at your factory today</p>
        </div>
        <button onclick="toggleFullscreen()" class="btn" style="border:1px solid rgba(255,255,255,0.2);background:rgba(255,255,255,0.08);color:white;backdrop-filter:blur(10px);" id="btnFs">
            <i class="bi bi-arrows-fullscreen"></i> Fullscreen
        </button>
    </div>
</div>

{{-- KPI Row 1 --}}
<div class="kpi-grid">
    <div class="kpi-card green">
        <div class="kpi-icon"><i class="bi bi-box-seam"></i></div>
        <div class="kpi-value">{{ number_format($stats['rawStock'] ?? 0, 1) }}</div>
        <div class="kpi-label">Raw Stock (kg)</div>
    </div>
    <div class="kpi-card blue">
        <div class="kpi-icon"><i class="bi bi-gear-wide-connected"></i></div>
        <div class="kpi-value">{{ $stats['activeBatches'] ?? 0 }}</div>
        <div class="kpi-label">Active Batches</div>
    </div>
    <div class="kpi-card amber">
        <div class="kpi-icon"><i class="bi bi-clock-history"></i></div>
        <div class="kpi-value">{{ $stats['pendingOrders'] ?? 0 }}</div>
        <div class="kpi-label">Pending Orders</div>
    </div>
    <div class="kpi-card violet">
        <div class="kpi-icon"><i class="bi bi-cup-straw"></i></div>
        <div class="kpi-value">{{ number_format($stats['totalFG'] ?? 0, 1) }}</div>
        <div class="kpi-label">Finished Goods</div>
    </div>
    <div class="kpi-card rose">
        <div class="kpi-icon"><i class="bi bi-trash3"></i></div>
        <div class="kpi-value">{{ $stats['wastePct'] ?? 0 }}%</div>
        <div class="kpi-label">Waste Rate</div>
    </div>
    <div class="kpi-card warm">
        <div class="kpi-icon"><i class="bi bi-currency-dollar"></i></div>
        <div class="kpi-value">${{ number_format($stats['totalRevenue'] ?? 0, 0) }}</div>
        <div class="kpi-label">Revenue</div>
    </div>
</div>

{{-- KPI Row 2 --}}
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div class="kpi-icon"><i class="bi bi-speedometer2"></i></div>
        <div class="kpi-value">{{ number_format($stats['avgOEE'] ?? 0, 1) }}%</div>
        <div class="kpi-label">Avg OEE (30d)</div>
    </div>
    <div class="kpi-card rose">
        <div class="kpi-icon"><i class="bi bi-exclamation-triangle"></i></div>
        <div class="kpi-value">{{ $stats['accidentsOpen'] ?? 0 }}</div>
        <div class="kpi-label">Open Incidents</div>
    </div>
    <div class="kpi-card amber">
        <div class="kpi-icon"><i class="bi bi-lightbulb"></i></div>
        <div class="kpi-value">{{ $stats['capaOpen'] ?? 0 }} / {{ $stats['capaOverdue'] ?? 0 }}</div>
        <div class="kpi-label">CAPA Open / Overdue</div>
    </div>
    <div class="kpi-card green">
        <div class="kpi-icon"><i class="bi bi-droplet"></i></div>
        <div class="kpi-value">{{ number_format($stats['waterTotal'] ?? 0, 0) }}L</div>
        <div class="kpi-label">Water (Monthly)</div>
    </div>
    <div class="kpi-card violet">
        <div class="kpi-icon"><i class="bi bi-lightning"></i></div>
        <div class="kpi-value">{{ number_format($stats['powerTotal'] ?? 0, 0) }}</div>
        <div class="kpi-label">kWh (Monthly)</div>
    </div>
    <div class="kpi-card warm">
        <div class="kpi-icon"><i class="bi bi-cpu"></i></div>
        <div class="kpi-value">{{ $stats['mDown'] ?? 0 }} / {{ $stats['mTotal'] ?? 0 }}</div>
        <div class="kpi-label">Machines Down</div>
    </div>
</div>

{{-- Charts --}}
<div class="charts-grid">
    <div class="glass-card chart-card">
        <div class="chart-title">
            <div class="chart-dot" style="background:var(--gradient-brand);"></div>
            Production by Flavour
        </div>
        <canvas id="flavourChart" height="220"></canvas>
    </div>
    <div class="glass-card chart-card">
        <div class="chart-title">
            <div class="chart-dot" style="background:var(--gradient-sky);"></div>
            Monthly Production
        </div>
        <canvas id="monthlyChart" height="220"></canvas>
    </div>
    <div class="glass-card chart-card">
        <div class="chart-title">
            <div class="chart-dot" style="background:var(--gradient-rose);"></div>
            Waste by Type
        </div>
        <canvas id="wasteChart" height="220"></canvas>
    </div>
    <div class="glass-card chart-card">
        <div class="chart-title">
            <div class="chart-dot" style="background:var(--gradient-cool);"></div>
            OEE by Machine
        </div>
        <canvas id="oeeChart" height="220"></canvas>
    </div>
</div>

{{-- Tables --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(400px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div class="glass-flat">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid rgba(0,0,0,0.05);display:flex;align-items:center;justify-content:space-between;">
            <span class="section-title"><i class="bi bi-gear-wide-connected" style="color:#22c55e;"></i> Recent Batches</span>
            <a href="{{ route('production.index') }}" style="font-size:0.72rem;font-weight:600;color:#2563eb;">View All</a>
        </div>
        <table class="data-table">
            <thead><tr><th>Batch</th><th>Flavour</th><th>Qty</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($recentBatches as $b)
                <tr>
                    <td style="font-weight:600;">{{ $b->BatchNumber ?? $b->BatchID }}</td>
                    <td>{{ $b->Flavour }}</td>
                    <td>{{ number_format($b->Quantity, 1) }}</td>
                    <td>
                        @php $s = $b->Status; @endphp
                        <span class="badge-status {{ match($s) { 'Completed'=>'badge-success', 'In Progress'=>'badge-info', 'Pending'=>'badge-warning', 'Rejected'=>'badge-danger', default=>'badge-muted' } }}">{{ $s }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:1.5rem;">No batches yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="glass-flat">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid rgba(0,0,0,0.05);display:flex;align-items:center;justify-content:space-between;">
            <span class="section-title"><i class="bi bi-cart3" style="color:#6366f1;"></i> Recent Orders</span>
        </div>
        <table class="data-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($recentOrders as $o)
                <tr>
                    <td style="font-weight:600;">{{ $o->OrderID }}</td>
                    <td>{{ $o->CustomerName }}</td>
                    <td style="font-weight:600;">${{ number_format($o->TotalAmount, 2) }}</td>
                    <td>
                        @php $s = $o->Status; @endphp
                        <span class="badge-status {{ match($s) { 'Completed'=>'badge-success', 'Processing'=>'badge-info', 'Pending'=>'badge-warning', 'Cancelled'=>'badge-danger', default=>'badge-muted' } }}">{{ $s }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:1.5rem;">No orders yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleFullscreen() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('mainArea');
    const btn = document.getElementById('btnFs');
    sidebar.classList.toggle('collapsed');
    main.classList.toggle('expanded');
    btn.innerHTML = sidebar.classList.contains('collapsed')
        ? '<i class="bi bi-fullscreen-exit"></i> Exit'
        : '<i class="bi bi-arrows-fullscreen"></i> Fullscreen';
    setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
}

document.addEventListener('DOMContentLoaded', function() {
    const defs = {
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } }, x: { grid: { display: false } } }
    };

    @if(!empty($productionByFlavour))
    new Chart(document.getElementById('flavourChart'), {
        type: 'bar',
        data: {
            labels: [@foreach($productionByFlavour as $f)'{{ $f->Flavour }}',@endforeach],
            datasets: [{ data: [@foreach($productionByFlavour as $f){{ $f->Total }},@endforeach], backgroundColor: ['#22c55e','#06b6d4','#f59e0b','#ef4444','#8b5cf6','#ec4899','#14b8a6','#f97316'], borderRadius: 8, borderSkipped: false }]
        }, options: defs
    });
    @endif

    @if(!empty($monthlyProduction))
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: [@foreach($monthlyProduction as $m)'{{ $m->Month }}',@endforeach],
            datasets: [{ data: [@foreach($monthlyProduction as $m){{ $m->Total }},@endforeach], borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true, tension: 0.4, pointRadius: 4, pointBackgroundColor: '#2563eb', borderWidth: 2 }]
        }, options: defs
    });
    @endif

    @if(!empty($wasteByType))
    new Chart(document.getElementById('wasteChart'), {
        type: 'doughnut',
        data: {
            labels: [@foreach($wasteByType as $w)'{{ $w->WasteType }}',@endforeach],
            datasets: [{ data: [@foreach($wasteByType as $w){{ $w->Total }},@endforeach], backgroundColor: ['#ef4444','#f97316','#f59e0b','#22c55e','#06b6d4','#8b5cf6'], borderWidth: 0, spacing: 2 }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { padding: 12, usePointStyle: true, pointStyle: 'circle' } } } }
    });
    @endif

    @if(!empty($oeeByMachine))
    new Chart(document.getElementById('oeeChart'), {
        type: 'bar',
        data: {
            labels: [@foreach($oeeByMachine as $o)'{{ $o->MachineName }}',@endforeach],
            datasets: [{ data: [@foreach($oeeByMachine as $o){{ number_format($o->AvgOEE, 1) }},@endforeach], backgroundColor: ['#8b5cf6','#6366f1','#a78bfa','#c4b5fd','#ddd6fe','#ede9fe'], borderRadius: 8, borderSkipped: false }]
        },
        options: { ...defs, scales: { ...defs.scales, y: { ...defs.scales.y, max: 100 } } }
    });
    @endif
});
</script>
@endsection
