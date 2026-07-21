// FreshJuice Factory Management System - Main JS
document.addEventListener('DOMContentLoaded', function () {

    /* ═══════════════════════════════════════════
       SIDEBAR — Collapsible + Mobile Menu
       ═══════════════════════════════════════════ */
    var sidebar = document.getElementById('sidebar');
    var wrapper = document.getElementById('wrapper');
    var sidebarToggler = document.querySelector('.sidebar-toggler');
    var menuToggler = document.querySelector('.menu-toggler');

    var collapsedSidebarHeight = '52px';
    var fullSidebarHeight = 'calc(100vh - 32px)';

    // Desktop: toggle collapsed state
    if (sidebarToggler && sidebar) {
        sidebarToggler.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            if (wrapper) {
                wrapper.classList.toggle('sidebar-collapsed', sidebar.classList.contains('collapsed'));
            }
        });
    }

    // Mobile: toggle menu-active (expand/collapse height)
    var toggleMenu = function (isMenuActive) {
        if (!sidebar || !menuToggler) return;
        if (isMenuActive) {
            sidebar.style.height = sidebar.scrollHeight + 'px';
        } else {
            sidebar.style.height = '';
        }
        var icon = menuToggler.querySelector('i');
        if (icon) {
            icon.className = isMenuActive ? 'bi bi-x-lg' : 'bi bi-list';
        }
    };

    if (menuToggler && sidebar) {
        menuToggler.addEventListener('click', function () {
            toggleMenu(sidebar.classList.toggle('menu-active'));
        });
    }

    // Window resize handler
    window.addEventListener('resize', function () {
        if (!sidebar) return;
        if (window.innerWidth > 1024) {
            sidebar.style.height = '';
            sidebar.classList.remove('menu-active');
        } else {
            sidebar.classList.remove('collapsed');
            if (wrapper) wrapper.classList.remove('sidebar-collapsed');
            toggleMenu(sidebar.classList.contains('menu-active'));
        }
    });

    // Mobile sidebar toggle from navbar button
    var sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            if (window.innerWidth <= 1024) {
                toggleMenu(sidebar.classList.toggle('menu-active'));
            } else {
                sidebar.classList.toggle('collapsed');
                if (wrapper) {
                    wrapper.classList.toggle('sidebar-collapsed', sidebar.classList.contains('collapsed'));
                }
            }
        });
    }

    /* ═══════════════════════════════════════════
       DATATABLES
       ═══════════════════════════════════════════ */
    if (typeof $.fn.DataTable !== 'undefined') {
        $('.table').each(function () {
            if ($(this).find('th').length > 0 && !$(this).closest('.no-datatable').length) {
                try {
                    $(this).DataTable({
                        order: [],
                        pageLength: 100,
                        lengthMenu: [10, 25, 50, 100],
                        language: { search: '', searchPlaceholder: 'Search...' },
                        responsive: true
                    });
                } catch (e) {}
            }
        });
    }

    /* ═══════════════════════════════════════════
       DELETE CONFIRMATIONS
       ═══════════════════════════════════════════ */
    document.querySelectorAll('a[data-confirm]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var href = this.getAttribute('href');
            Swal.fire({
                title: 'Are you sure?',
                text: this.dataset.confirm || 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then(function (result) {
                if (result.isConfirmed) window.location.href = href;
            });
        });
    });

    /* ═══════════════════════════════════════════
       CHART.JS DEFAULTS
       ═══════════════════════════════════════════ */
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', system-ui, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
    }

    /* ═══════════════════════════════════════════
       AUTO-DISMISS ALERTS
       ═══════════════════════════════════════════ */
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () { bootstrap.Alert.getOrCreateInstance(alert).close(); }, 5000);
    });

    /* ═══════════════════════════════════════════
       KEYBOARD SHORTCUTS
       ═══════════════════════════════════════════ */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            var fs = document.getElementById('wrapper');
            if (fs && fs.classList.contains('dashboard-fullscreen')) {
                if (typeof toggleDashboardFullscreen === 'function') toggleDashboardFullscreen();
            }
        }
    });
});
