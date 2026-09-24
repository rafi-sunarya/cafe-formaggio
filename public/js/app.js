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

});