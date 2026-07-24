/**
 * Café Aurora — Admin Panel JavaScript
 */

document.addEventListener('DOMContentLoaded', function () {
    initSidebarToggle();
    initDarkMode();
    initToasts();
    initTableSearch();
    initDeleteConfirm();
});

/**
 * Sidebar toggle para responsive
 */
function initSidebarToggle() {
    const toggleBtn = document.getElementById('btn-toggle-sidebar');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('sidebar-overlay');

    if (!toggleBtn || !sidebar) return;

    toggleBtn.addEventListener('click', function () {
        sidebar.classList.toggle('show');
        if (overlay) overlay.classList.toggle('show');
    });

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
}

/**
 * Dark mode toggle
 */
function initDarkMode() {
    const toggleBtn = document.getElementById('btn-toggle-theme');
    if (!toggleBtn) return;

    const savedTheme = localStorage.getItem('admin-theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(toggleBtn, savedTheme);

    toggleBtn.addEventListener('click', function () {
        const current = document.documentElement.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('admin-theme', next);
        updateThemeIcon(toggleBtn, next);
    });
}

function updateThemeIcon(btn, theme) {
    const icon = btn.querySelector('i');
    if (!icon) return;
    icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-fill';
}

/**
 * Auto-show Bootstrap toasts
 */
function initToasts() {
    document.querySelectorAll('.toast[data-bs-autohide]').forEach(function (el) {
        const toast = new bootstrap.Toast(el);
        toast.show();
    });
}

/**
 * Búsqueda en tablas admin
 */
function initTableSearch() {
    const searchInput = document.getElementById('admin-table-search');
    if (!searchInput) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const tableBody = document.querySelector('.admin-table .table tbody');
        if (!tableBody) return;

        tableBody.querySelectorAll('tr').forEach(function (row) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

/**
 * Confirmación antes de eliminar
 */
function initDeleteConfirm() {
    document.querySelectorAll('.btn-delete-confirm').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('¿Estás seguro de que deseas eliminar este registro? Esta acción no se puede deshacer.')) {
                e.preventDefault();
            }
        });
    });
}
