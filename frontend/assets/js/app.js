// FreshJuice Factory Management System - Main JS
document.addEventListener('DOMContentLoaded', function () {

    // DataTables initialization
    document.querySelectorAll('#dataTable').forEach(function (table) {
        new DataTable(table, {
            pageLength: 10,
            order: [[0, 'desc']],
            language: {
                search: '<i class="bi bi-search"></i>',
                lengthMenu: 'Show _MENU_',
                info: 'Showing _START_ to _END_ of _TOTAL_',
                paginate: { previous: '<i class="bi bi-chevron-left"></i>', next: '<i class="bi bi-chevron-right"></i>' }
            },
            dom: '<"row mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    });

    // Sidebar toggle
    var toggleBtn = document.getElementById('toggle-sidebar');
    var sidebar = document.getElementById('sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            sidebar.classList.toggle('show');
        });
    }

    // Delete confirmations with SweetAlert
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

    // Chart.js default config
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.font.size = 13;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
    }

    // Auto-dismiss alerts
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () { bootstrap.Alert.getOrCreateInstance(alert).close(); }, 5000);
    });

    // Active nav highlighting
    var params = new URLSearchParams(window.location.search);
    var currentRoute = params.get('route') || 'dashboard';
    document.querySelectorAll('#sidebar .nav-link').forEach(function (link) {
        var href = link.getAttribute('href') || '';
        var linkRoute = href.replace('?route=', '');
        if (currentRoute === linkRoute || currentRoute.startsWith(linkRoute + '/')) {
            link.classList.add('active');
        }
    });
});
