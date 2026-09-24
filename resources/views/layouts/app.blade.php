<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cafe Formaggio')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-icon">☕</div>
            <div>
                <div class="brand-name">Cafe Formaggio</div>
                <div class="brand-subtitle">Gateway Monitoring</div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">MAIN MENU</div>
            <a class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i data-lucide="layout-dashboard"></i>Dashboard</a>
            <a class="nav-item {{ request()->routeIs('history.*') ? 'active' : '' }}" href="{{ route('history.index') }}"><i data-lucide="history"></i>Histori Koneksi</a>
            <a class="nav-item" href="#"><i data-lucide="chart-no-axes-combined"></i>Laporan <span class="coming">Soon</span></a>

            <div class="nav-label">MONITORING</div>
            <a class="nav-item {{ request()->routeIs('targets.*') ? 'active' : '' }}" href="{{ route('targets.index') }}"><i data-lucide="router"></i>Gateway / Target</a>
            <a class="nav-item {{ request()->routeIs('incidents.*') ? 'active' : '' }}" href="{{ route('incidents.index') }}"><i data-lucide="triangle-alert"></i>Gangguan</a>

            <div class="nav-label">ADMIN</div>
            <a class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i data-lucide="users"></i>Pengguna</a>
            <a class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.index') }}"><i data-lucide="settings"></i>Pengaturan</a>
        </nav>

        <div class="sidebar-bottom">
            <div class="user-mini"><div class="avatar">A</div><div><strong>Admin</strong><small>Administrator</small></div></div>
            <form action="{{ route('logout') }}" method="POST">
    @csrf

    <button type="submit" class="logout-link">
        <i data-lucide="log-out"></i>
        Logout
    </button>
</form>
        </div>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <button class="icon-button" id="sidebarToggle"><i data-lucide="menu"></i></button>
            <div class="topbar-right">
    <span class="status-dot"></span>
    <span>Gateway Cafe</span>

    <div class="user-dropdown">
        <button type="button" class="user-menu-button" id="userMenuToggle">
            <div class="avatar small">A</div>
            <span>Admin</span>
            <i data-lucide="chevron-down" class="chevron"></i>
        </button>

        <div class="user-menu" id="userMenu">
            <a href="#" class="user-menu-item">
                <i data-lucide="user-round"></i>
                <span>Profile</span>
            </a>

            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="user-menu-item logout-item">
                    <i data-lucide="log-out"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
        </header>

        <section class="page-content">
            @yield('content')
        </section>
    </main>
</div>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
