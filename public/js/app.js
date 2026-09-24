document.addEventListener('DOMContentLoaded', () => {

    if (window.lucide) lucide.createIcons();

    // Sidebar Toggle
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
        });
    }

    // User Dropdown Toggle
    const userMenuToggle = document.getElementById('userMenuToggle');
    const userMenu = document.getElementById('userMenu');

    if (userMenuToggle && userMenu) {

        userMenuToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            userMenu.classList.toggle('show');
        });

        document.addEventListener('click', (event) => {
            if (!userMenu.contains(event.target) &&
                !userMenuToggle.contains(event.target)) {
                userMenu.classList.remove('show');
            }
        });

    }

    // Search Gateway / Target
    const searchInput = document.getElementById('targetSearch');
    const table = document.getElementById('targetsTable');

    if (!searchInput || !table) {
        return;
    }

    const rows = table.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', () => {
        const keyword = searchInput.value.toLowerCase().trim();

        rows.forEach((row) => {

            // Lewati baris kosong
            if (row.cells.length < 7) {
                return;
            }

            const name = row.cells[1].textContent.toLowerCase();
            const ipAddress = row.cells[2].textContent.toLowerCase();

            const isMatch =
                name.includes(keyword) ||
                ipAddress.includes(keyword);

            row.style.display = isMatch ? '' : 'none';

        });

    });

});