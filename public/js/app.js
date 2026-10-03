document.addEventListener("DOMContentLoaded", () => {
    // =====================================================
    // LUCIDE ICON
    // =====================================================
    if (window.lucide) {
        lucide.createIcons();
    }

    // =====================================================
    // ROLE RESTRICTION
    // =====================================================
    window.showRoleRestriction = function (event, element) {
        event.preventDefault();

        const role = document.body.dataset.role || "user";
        const feature = element?.dataset.feature || "Fitur ini";

        const roleName = {
            admin: "admin",
            teknisi: "teknisi",
            pemilik: "pemilik",
        };

        const currentRole = roleName[role] || role;

        showRestrictionToast(
            `${feature} tidak tersedia, anda masuk sebagai ${currentRole}.`,
        );
    };

    // =====================================================
    // SIDEBAR TOGGLE
    // =====================================================
    const sidebar = document.querySelector(".sidebar");
    const sidebarToggle = document.getElementById("sidebarToggle");

    sidebarToggle?.addEventListener("click", (e) => {
        e.stopPropagation();

        if (!sidebar) return;

        sidebar.classList.toggle("collapsed");
    });

    sidebar?.addEventListener("click", (e) => {
        e.stopPropagation();
    });

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

    // =====================================================
    // USER DROPDOWN
    // =====================================================
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

    // =====================================================
    // SEARCH GATEWAY / TARGET
    // =====================================================
    const searchInput = document.getElementById("targetSearch");
    const table = document.getElementById("targetsTable");

    // Jangan return dari seluruh script.
    // Search hanya dijalankan kalau elemennya memang ada.
    if (searchInput && table) {
        const rows = table.querySelectorAll("tbody tr");

        searchInput.addEventListener("input", () => {
            const keyword = searchInput.value.toLowerCase().trim();

            rows.forEach((row) => {
                if (row.cells.length < 7) {
                    return;
                }

                const name = row.cells[1].textContent.toLowerCase().trim();

                const ipAddress = row.cells[2].textContent.toLowerCase().trim();

                const isMatch =
                    name.includes(keyword) || ipAddress.includes(keyword);

                row.style.display = isMatch ? "" : "none";
            });
        });
    }

    // =====================================================
    // RESTRICTION TOAST
    // =====================================================
    function showRestrictionToast(message) {
        // Hapus toast lama
        const oldToast = document.querySelector(".restriction-toast");

        if (oldToast) {
            oldToast.remove();
        }

        // Buat toast
        const toast = document.createElement("div");

        toast.className = "restriction-toast";

        toast.innerHTML = `
            <div class="restriction-toast-icon">
                <i data-lucide="lock"></i>
            </div>

            <div class="restriction-toast-content">
                <strong>Akses Terbatas</strong>
                <span>${message}</span>
            </div>

            <button
                type="button"
                class="restriction-toast-close"
                aria-label="Tutup"
            >
                <i data-lucide="x"></i>
            </button>
        `;

        document.body.appendChild(toast);

        // Aktifkan Lucide
        if (window.lucide) {
            lucide.createIcons();
        }

        // Tombol close
        const closeButton = toast.querySelector(".restriction-toast-close");

        closeButton?.addEventListener("click", () => {
            toast.classList.remove("show");

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        });

        // Animasi masuk
        requestAnimationFrame(() => {
            toast.classList.add("show");
        });

        // Hilang otomatis setelah 4 detik
        setTimeout(() => {
            if (!toast.parentElement) {
                return;
            }

            toast.classList.remove("show");

            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 4000);
    }
});
