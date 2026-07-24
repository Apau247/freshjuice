// FreshJuice Factory Management System - Main JS
document.addEventListener('DOMContentLoaded', function () {

    /* ═══════════════════════════════════════════
       SCROLL POSITION — Save/Restore across page loads
       ═══════════════════════════════════════════ */
    var scrollKey = 'fj_scroll_' + window.location.search;
    var savedScroll = sessionStorage.getItem(scrollKey);
    if (savedScroll) {
        window.scrollTo(0, parseInt(savedScroll, 10));
        sessionStorage.removeItem(scrollKey);
    }
    window.addEventListener('beforeunload', function () {
        sessionStorage.setItem(scrollKey, window.scrollY);
    });

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
       LOGOUT — Convert GET to POST
       ═══════════════════════════════════════════ */
    document.querySelectorAll('a[href*="auth/logout"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = this.getAttribute('href');
            var csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'csrf_token';
                hidden.value = csrfMeta.getAttribute('content');
                form.appendChild(hidden);
            }
            document.body.appendChild(form);
            form.submit();
        });
    });

    /* ═══════════════════════════════════════════
       DELETE LINKS — Convert GET to POST with CSRF
       ═══════════════════════════════════════════ */
    document.querySelectorAll('a[href*="/delete"]').forEach(function (link) {
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
                if (result.isConfirmed) {
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = href;
                    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    if (csrfMeta) {
                        var hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'csrf_token';
                        hidden.value = csrfMeta.getAttribute('content');
                        form.appendChild(hidden);
                    }
                    document.body.appendChild(form);
                    form.submit();
                }
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
