document.addEventListener("DOMContentLoaded", () => {
    if (window.lucide) lucide.createIcons();

    // Sidebar Toggle
    // Sidebar Toggle
    const sidebar = document.querySelector(".sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");

    sidebarToggle?.addEventListener("click", (e) => {
        e.stopPropagation();

        if (!sidebar) return;

        sidebar.classList.toggle("collapsed");
    });

    // Jangan tutup kalau klik di dalam sidebar
    sidebar?.addEventListener("click", (e) => {
        e.stopPropagation();
    });

    // Tutup sidebar kalau klik area di luar sidebar
    document.addEventListener("click", (e) => {
        if (window.innerWidth > 800) return;

        if (
            sidebar &&
            !sidebar.contains(e.target) &&
            !sidebarToggle?.contains(e.target)
        ) {
            sidebar.classList.remove("collapsed");
        }
    });

    // User Dropdown Toggle
    const userMenuToggle = document.getElementById("userMenuToggle");
    const userMenu = document.getElementById("userMenu");

    if (userMenuToggle && userMenu) {
        userMenuToggle.addEventListener("click", (event) => {
            event.stopPropagation();
            userMenu.classList.toggle("show");
        });

        document.addEventListener("click", (event) => {
            if (
                !userMenu.contains(event.target) &&
                !userMenuToggle.contains(event.target)
            ) {
                userMenu.classList.remove("show");
            }
        });
    }

    // Search Gateway / Target
    const searchInput = document.getElementById("targetSearch");
    const table = document.getElementById("targetsTable");

    if (!searchInput || !table) {
        return;
    }

    const rows = table.querySelectorAll("tbody tr");

    searchInput.addEventListener("input", () => {
        const keyword = searchInput.value.toLowerCase().trim();

        rows.forEach((row) => {
            // Lewati baris kosong
            if (row.cells.length < 7) {
                return;
            }

            const name = row.cells[1].textContent.toLowerCase();
            const ipAddress = row.cells[2].textContent.toLowerCase();

            const isMatch =
                name.includes(keyword) || ipAddress.includes(keyword);

            row.style.display = isMatch ? "" : "none";
        });
    });
});
