<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle ?? 'Dashboard' }} — FreshJuice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @yield('head')
</head>
<body>
    <div class="app-shell">
        {{-- Sidebar --}}
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div style="width:32px;height:32px;border-radius:10px;background:var(--gradient-brand);display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-cup-straw" style="color:white;font-size:0.9rem;"></i>
                </div>
                <span class="brand-text">FreshJuice</span>
            </div>

            <nav class="sidebar-nav">
                @php $current = request()->route()->getName(); @endphp

                <a href="{{ route('dashboard') }}" class="nav-item {{ $current === 'dashboard' ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
                </a>

                <div class="nav-section">Suppliers & Materials</div>
                <a href="{{ route('suppliers.index') }}" class="nav-item {{ str_starts_with($current, 'suppliers') ? 'active' : '' }}">
                    <i class="bi bi-truck"></i><span>Suppliers</span>
                </a>

                <div class="nav-section">Production</div>
                <a href="{{ route('production.index') }}" class="nav-item {{ str_starts_with($current, 'production') ? 'active' : '' }}">
                    <i class="bi bi-gear-wide-connected"></i><span>Batches</span>
                </a>
                <a href="{{ route('quality.index') }}" class="nav-item {{ str_starts_with($current, 'quality') ? 'active' : '' }}">
                    <i class="bi bi-patch-check"></i><span>Quality</span>
                </a>

                <div class="nav-section">Safety & Compliance</div>
                <div class="nav-section">Workforce</div>
                <div class="nav-section">Assets</div>
                <div class="nav-section">Admin</div>
            </nav>
        </aside>

        {{-- Main area --}}
        <main class="main-area" id="mainArea">
            {{-- Top bar --}}
            <header class="topbar">
                <button class="topbar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="topbar-title">{{ $pageTitle ?? 'Dashboard' }}</h1>
                <div class="topbar-actions">
                    <div class="topbar-user">
                        <div class="avatar">{{ strtoupper(substr(session('user_name', 'U'), 0, 1)) }}</div>
                        <span>{{ session('user_name', 'User') }}</span>
                    </div>
                    <a href="{{ route('logout') }}" class="topbar-btn" title="Sign out">
                        <i class="bi bi-box-arrow-right"></i>
                    </a>
                </div>
            </header>

            {{-- Page content --}}
            <div class="page-content">
                @if(session('success'))
                    <div style="background:#dcfce7;color:#16a34a;border-radius:10px;padding:0.6rem 0.85rem;font-size:0.82rem;font-weight:500;margin-bottom:1rem;display:flex;align-items:center;gap:0.4rem;">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="background:#fee2e2;color:#dc2626;border-radius:10px;padding:0.6rem 0.85rem;font-size:0.82rem;font-weight:500;margin-bottom:1rem;display:flex;align-items:center;gap:0.4rem;">
                        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainArea').classList.toggle('expanded');
        }
    </script>
    @yield('scripts')
</body>
</html>
