// FreshJuice Factory Management System - Main JS
document.addEventListener('DOMContentLoaded', function () {

    /* ═══════════════════════════════════════════
       SCROLL POSITION — Preserve page + sidebar scroll
       across in-app navigation (don't jump to top)
       ═══════════════════════════════════════════ */
    var SCROLL_KEY = 'fj_scroll_pos';
    var SIDEBAR_SCROLL_KEY = 'fj_sidebar_scroll_pos';

    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    var primaryNav = document.querySelector('.sidebar-nav .primary-nav');
    var scrollTimer = null;
    var isLoggingOut = false;

    var saveScroll = function () {
        if (isLoggingOut) return;
        sessionStorage.setItem(SCROLL_KEY, String(window.scrollY || document.documentElement.scrollTop || 0));
        if (primaryNav) {
            sessionStorage.setItem(SIDEBAR_SCROLL_KEY, String(primaryNav.scrollTop || 0));
        }
    };

    var scheduleSave = function () {
        if (scrollTimer) clearTimeout(scrollTimer);
        scrollTimer = setTimeout(saveScroll, 60);
    };

    var restoreScroll = function () {
        var savedScroll = parseInt(sessionStorage.getItem(SCROLL_KEY), 10) || 0;
        if (savedScroll > 0 && (window.scrollY || document.documentElement.scrollTop) === 0) {
            window.scrollTo(0, savedScroll);
        }
        if (primaryNav) {
            var savedSidebarScroll = parseInt(sessionStorage.getItem(SIDEBAR_SCROLL_KEY), 10) || 0;
            if (savedSidebarScroll > 0) {
                primaryNav.scrollTop = savedSidebarScroll;
            }
            var activeLink = primaryNav.querySelector('.nav-link.active');
            if (activeLink) {
                var navRect = primaryNav.getBoundingClientRect();
                var linkRect = activeLink.getBoundingClientRect();
                if (linkRect.bottom > navRect.bottom) {
                    primaryNav.scrollTop += (linkRect.bottom - navRect.bottom) + 8;
                }
            }
        }
    };

    restoreScroll();
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', restoreScroll);
    }
    window.addEventListener('load', restoreScroll);
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) restoreScroll();
    });

    window.addEventListener('scroll', scheduleSave, { passive: true });
    if (primaryNav) {
        primaryNav.addEventListener('scroll', scheduleSave, { passive: true });
    }

    // Flush the saved position the instant the user leaves the page.
    // This guarantees the sidebar keeps its place on back/forward
    // navigation even if the debounced scroll save hadn't fired yet.
    window.addEventListener('pagehide', saveScroll);
    window.addEventListener('beforeunload', saveScroll);
    document.addEventListener('click', function (e) {
        var link = e.target && e.target.closest ? e.target.closest('a') : null;
        if (link && link.hostname === window.location.hostname) {
            saveScroll();
        }
    }, true);

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
    // Guard on jQuery itself: if the CDN is blocked, a bare $.fn lookup throws a
    // ReferenceError and every handler registered below this point is lost.
    if (typeof $ !== 'undefined' && $.fn && typeof $.fn.DataTable !== 'undefined') {
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
            isLoggingOut = true;
            sessionStorage.removeItem('fj_scroll_pos');
            sessionStorage.removeItem('fj_sidebar_scroll_pos');
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
       BARCODE SCAN — modal focus + ?q= list filter
       ═══════════════════════════════════════════ */
    var scanModal = document.getElementById('scanModal');
    if (scanModal && typeof bootstrap !== 'undefined') {
        scanModal.addEventListener('shown.bs.modal', function () {
            var input = document.getElementById('scanInput');
            if (input) { input.value = ''; input.focus(); }
        });
    }

    // ScanController redirects to list pages with &q=<code>; push the code
    // into the DataTable search box so the scanned record is isolated.
    var urlQ = new URLSearchParams(window.location.search).get('q');
    if (urlQ && typeof $ !== 'undefined' && $.fn && typeof $.fn.DataTable !== 'undefined') {
        var applySearch = function () {
            var api = $('.table').DataTable();
            api.search(String(urlQ)).draw();
            var box = document.querySelector('.dataTables_filter input');
            if (box) box.value = urlQ;
        };
        if ($.fn.DataTable.isDataTable('.table')) {
            applySearch();
        } else {
            setTimeout(applySearch, 300);
        }
    }

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

    /* ═══════════════════════════════════════════
       FORM VALIDATION NET — runs on every form.
       Number fields: rejects blanks/negatives/out-of-range values with an
       inline error; dates respect min/max; required textboxes must be filled.
       Server-side validation still guards every module -- this just keeps the
       UX smooth and catches mistakes before a submit round-trip.
       ═══════════════════════════════════════════ */
    var ensureFeedback = function (field) {
        if (field.parentElement.querySelector('.invalid-feedback')) return;
        var msg = document.createElement('div');
        msg.className = 'invalid-feedback';
        field.parentElement.appendChild(msg);
    };

    var messageFor = function (field) {
        if (field.validity.valueMissing) return 'This field is required.';
        if (field.validity.badInput)   return 'Please enter a valid number.';
        if (field.validity.rangeUnderflow) return 'Value cannot be less than ' + field.min + '.';
        if (field.validity.rangeOverflow)  return 'Value cannot be greater than ' + field.max + '.';
        if (field.validity.stepMismatch) return 'Please use steps of ' + (field.step || '1') + '.';
        if (field.validity.tooLong) return 'Too many characters.';
        return field.validationMessage || 'Invalid value.';
    };

    var validateField = function (field) {
        // Only enforce custom checks on inputs that actually declare constraints;
        // everything else falls through to native HTML5 validation.
        var constrained = field.required || field.hasAttribute('min') ||
                          field.hasAttribute('max') || field.pattern;
        if (!constrained) { field.classList.remove('is-invalid'); return true; }

        var ok = field.checkValidity();
        if (!ok) {
            ensureFeedback(field);
            var fb = field.parentElement.querySelector('.invalid-feedback');
            if (fb) fb.textContent = messageFor(field);
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
        return ok;
    };

    document.querySelectorAll('form').forEach(function (form) {
        // Live feedback: clear the red border as soon as the user fixes a field.
        form.addEventListener('input', function (e) {
            var f = e.target;
            if (f.classList && f.classList.contains('is-invalid') && f.checkValidity()) {
                f.classList.remove('is-invalid');
            }
        });

        form.addEventListener('submit', function (e) {
            if (form.dataset.validated === '1') return; // already passed once
            var fields = form.querySelectorAll('input, select, textarea');
            var firstBad = null;

            fields.forEach(function (f) {
                // Skip buttons/hidden/csrf inputs
                if (!['INPUT', 'SELECT', 'TEXTAREA'].includes(f.tagName)) return;
                if (f.type === 'hidden') return;
                if (!validateField(f) && !firstBad) firstBad = f;
            });

            if (firstBad) {
                e.preventDefault();
                firstBad.focus();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Check the highlighted field',
                        text: firstBad.parentElement.querySelector('.invalid-feedback')
                            ? firstBad.parentElement.querySelector('.invalid-feedback').textContent
                            : 'Some fields need your attention.',
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 3500
                    });
                }
                return;
            }
            form.dataset.validated = '1'; // allow the submit; reset for back-nav
            setTimeout(function () { delete form.dataset.validated; }, 5000);
        });
    });
});
