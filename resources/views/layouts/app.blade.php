<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cafe Formaggio')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body data-role="{{ auth()->user()->role ?? '' }}">
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

    {{-- Dashboard - SEMUA ROLE --}}
    <a
        class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"
        href="{{ route('dashboard') }}"
    >
        <i data-lucide="layout-dashboard"></i>
        <span>Dashboard</span>
    </a>


    {{-- Histori - SEMUA ROLE --}}
    <a
        class="nav-item {{ request()->routeIs('history.*') ? 'active' : '' }}"
        href="{{ route('history.index') }}"
    >
        <i data-lucide="history"></i>
        <span>Histori Koneksi</span>
    </a>


    {{-- Laporan - ADMIN + PEMILIK --}}
    @if(in_array(auth()->user()->role, ['admin', 'pemilik']))

        <a
            class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}"
            href="{{ route('reports.index') }}"
        >
            <i data-lucide="chart-no-axes-combined"></i>
            <span>Laporan</span>
        </a>

    @else

        <a
            href="#"
            class="nav-item locked-nav"
            onclick="showRoleRestriction(event, this)"
        >
            <i data-lucide="chart-no-axes-combined"></i>
            <span>Laporan</span>
            <i data-lucide="lock" class="nav-lock"></i>
        </a>

    @endif


    <div class="nav-label">MONITORING</div>


    {{-- Gateway / Target - TEKNISI --}}
    @if(auth()->user()->role === 'teknisi')

        <a
            class="nav-item {{ request()->routeIs('targets.*') ? 'active' : '' }}"
            href="{{ route('targets.index') }}"
        >
            <i data-lucide="router"></i>
            <span>Gateway / Target</span>
        </a>

    @else

        <a
            href="#"
            class="nav-item locked-nav"
            onclick="showRoleRestriction(event, this)"
        >
            <i data-lucide="router"></i>
            <span>Gateway / Target</span>
            <i data-lucide="lock" class="nav-lock"></i>
        </a>

    @endif


    {{-- Gangguan - ADMIN + TEKNISI --}}
    @if(in_array(auth()->user()->role, ['admin', 'teknisi']))

        <a
            class="nav-item {{ request()->routeIs('incidents.*') ? 'active' : '' }}"
            href="{{ route('incidents.index') }}"
        >
            <i data-lucide="triangle-alert"></i>
            <span>Gangguan</span>
        </a>

    @else

        <a
            href="#"
            class="nav-item locked-nav"
            onclick="showRoleRestriction(event, this)"
        >
            <i data-lucide="triangle-alert"></i>
            <span>Gangguan</span>
            <i data-lucide="lock" class="nav-lock"></i>
        </a>

    @endif


    <div class="nav-label">ADMIN</div>


    {{-- Pengguna - ADMIN --}}
    @if(auth()->user()->role === 'admin')

        <a
            class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}"
            href="{{ route('users.index') }}"
        >
            <i data-lucide="users"></i>
            <span>Pengguna</span>
        </a>

    @else

        <a
            href="#"
            class="nav-item locked-nav"
            onclick="showRoleRestriction(event, this)"
        >
            <i data-lucide="users"></i>
            <span>Pengguna</span>
            <i data-lucide="lock" class="nav-lock"></i>
        </a>

    @endif


    {{-- Pengaturan - TEKNISI --}}
    @if(auth()->user()->role === 'teknisi')

        <a
            class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}"
            href="{{ route('settings.index') }}"
        >
            <i data-lucide="settings"></i>
            <span>Pengaturan</span>
        </a>

    @else

        <a
            href="#"
            class="nav-item locked-nav"
            onclick="showRoleRestriction(event, this)"
        >
            <i data-lucide="settings"></i>
            <span>Pengaturan</span>
            <i data-lucide="lock" class="nav-lock"></i>
        </a>

    @endif

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
<span class="status-dot {{ isset($latestLog) && $latestLog?->status === 'offline' ? 'offline' : '' }}"></span>
<span>
    {{ $selectedGateway?->name ?? 'Semua Gateway' }}
</span>

    <div class="user-dropdown">
        <button type="button" class="user-menu-button" id="userMenuToggle">
            <div class="avatar small">A</div>
            <span>{{ ucfirst(auth()->user()->role) }}</span>
            <i data-lucide="chevron-down" class="chevron"></i>
        </button>

        <div class="user-menu" id="userMenu">
            

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

        <footer class="app-footer">
    <div class="footer-brand">
        <span class="footer-dot"></span>
        <span>Cafe Formaggio</span>
    </div>

    <div class="footer-info">
        Gateway Monitoring
        <span>•</span>
        {{ date('Y') }}
    </div>
</footer>


<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
