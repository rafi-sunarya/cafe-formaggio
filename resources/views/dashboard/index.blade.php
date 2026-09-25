@extends('layouts.app')

@section('title', 'Dashboard - Cafe Formaggio')

@section('content')

<div class="page-heading">
    <div>
        <div class="breadcrumb">Home / Dashboard</div>
        <h1>Dashboard</h1>
        <p>Pantau kualitas koneksi gateway Cafe Formaggio secara berkala.</p>
    </div>

    
<div class="target-select">
    <label for="gateway_id">Target Monitoring</label>

    <form
        action="{{ route('dashboard') }}"
        method="GET"
    >
        <select
            name="gateway_id"
            id="gateway_id"
            onchange="this.form.submit()"
        >
            <option value="">
                Semua Gateway
            </option>

            @foreach ($gateways as $gateway)
                <option
                    value="{{ $gateway->id }}"
                    @selected(
                        (string) $selectedGatewayId ===
                        (string) $gateway->id
                    )
                >
                    {{ $gateway->name }}
                    ({{ $gateway->ip_address }})
                </option>
            @endforeach
        </select>
    </form>
</div>
</div>

{{-- Alert Monitoring --}}
<div class="alert success">
    <i data-lucide="check-circle"></i>

    <span>
        @if ($latestLog)
            Data terakhir diperbarui pada
            {{ $latestLog->checked_at->format('d F Y, H:i:s') }}.
        @else
            Belum ada data monitoring.
        @endif
    </span>
</div>

{{-- KPI CARDS --}}
<div class="kpi-grid">

    {{-- Status Gateway --}}
    <div class="kpi-card">
        <div class="kpi-icon green">
            <i data-lucide="wifi"></i>
        </div>

        <div>
            <span>Status</span>

            @if ($latestLog && $latestLog->status === 'online')
                <strong class="green-text">Online</strong>
                <small>Gateway dapat dijangkau</small>
            @elseif ($latestLog)
                <strong class="red-text">Offline</strong>
                <small>Gateway tidak dapat dijangkau</small>
            @else
                <strong>Belum Dicek</strong>
                <small>Belum ada data monitoring</small>
            @endif
        </div>
    </div>

    {{-- Latency --}}
    <div class="kpi-card">
        <div class="kpi-icon blue">
            <i data-lucide="gauge"></i>
        </div>

        <div>
            <span>Latency</span>
            <strong>{{ $averageLatency }} ms</strong>
            <small>Rata-rata 24 jam</small>
        </div>
    </div>

    {{-- Packet Loss --}}
    <div class="kpi-card">
        <div class="kpi-icon orange">
            <i data-lucide="database"></i>
        </div>

        <div>
            <span>Packet Loss</span>
            <strong>{{ $packetLoss }}%</strong>
            <small>Estimasi berdasarkan sampel</small>
        </div>
    </div>

    {{-- Uptime --}}
    <div class="kpi-card">
        <div class="kpi-icon purple">
            <i data-lucide="shield-check"></i>
        </div>

        <div>
            <span>Uptime (24 Jam)</span>
            <strong>{{ $uptime }}%</strong>
            <small>Availability berbasis sampel</small>
        </div>
    </div>

</div>

{{-- GRAFIK --}}
<div class="charts-grid">

    {{-- Grafik Latency --}}
    <div class="panel">
        <div class="panel-heading">
            <h3>
                <i data-lucide="chart-line"></i>
                Grafik Latency Gateway
            </h3>

            <div class="period-tabs">
                <button class="active">24 Jam</button>
                <button>7 Hari</button>
                <button>30 Hari</button>
            </div>
        </div>

        <div class="fake-chart line-chart">
            <div class="y-labels">
    <span>{{ number_format($maxLatency, 0) }}</span>
    <span>{{ number_format($maxLatency * 0.75, 0) }}</span>
    <span>{{ number_format($maxLatency * 0.5, 0) }}</span>
    <span>{{ number_format($maxLatency * 0.25, 0) }}</span>
    <span>0</span>
</div>

            <svg
                viewBox="0 0 600 220"
                preserveAspectRatio="none"
            >
                <g class="grid-lines">
                    <line x1="0" y1="20" x2="600" y2="20"/>
                    <line x1="0" y1="65" x2="600" y2="65"/>
                    <line x1="0" y1="110" x2="600" y2="110"/>
                    <line x1="0" y1="155" x2="600" y2="155"/>
                    <line x1="0" y1="200" x2="600" y2="200"/>
                </g>

                <polyline
                    class="line"
                    points="{{ $latencyPoints }}"
                />
            </svg>
        </div>

        <div class="chart-x">
            <span>00:00</span>
            <span>04:00</span>
            <span>08:00</span>
            <span>12:00</span>
            <span>16:00</span>
            <span>20:00</span>
            <span>24:00</span>
        </div>
    </div>

    {{-- Grafik Packet Loss --}}
    <div class="panel">
        <div class="panel-heading">
            <h3>
                <i data-lucide="chart-no-axes-combined"></i>
                Grafik Packet Loss
            </h3>

            <div class="period-tabs">
                <button class="active">24 Jam</button>
                <button>7 Hari</button>
                <button>30 Hari</button>
            </div>
        </div>

        <div class="fake-chart line-chart">
            <div class="y-labels">
    <span>100</span>
    <span>75</span>
    <span>50</span>
    <span>25</span>
    <span>0</span>
</div>

            <svg
                viewBox="0 0 600 220"
                preserveAspectRatio="none"
            >
                <g class="grid-lines">
                    <line x1="0" y1="20" x2="600" y2="20"/>
                    <line x1="0" y1="65" x2="600" y2="65"/>
                    <line x1="0" y1="110" x2="600" y2="110"/>
                    <line x1="0" y1="155" x2="600" y2="155"/>
                    <line x1="0" y1="200" x2="600" y2="200"/>
                </g>

                <polyline
                    class="line orange-line"
                    points="{{ $packetLossPoints }}"
                />
            </svg>
        </div>

        <div class="chart-x">
            <span>00:00</span>
            <span>04:00</span>
            <span>08:00</span>
            <span>12:00</span>
            <span>16:00</span>
            <span>20:00</span>
            <span>24:00</span>
        </div>
    </div>

</div>

{{-- MONITORING TERBARU & RINGKASAN --}}
<div class="bottom-grid">

    {{-- Monitoring Terbaru --}}
    <div class="panel">
        <div class="panel-heading">
            <h3>
                <i data-lucide="list"></i>
                Monitoring Terbaru
            </h3>

            <a
                href="{{ route('history.index') }}"
                class="text-link"
            >
                Lihat semua
            </a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Latency</th>
                        <th>Packet Loss</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($latestLogs as $i => $log)
                        <tr>
                            <td>{{ $i + 1 }}</td>

                            <td>
                                {{ $log->checked_at->format('d M Y, H:i:s') }}
                            </td>

                            <td>
                                {{ $log->response_time !== null
                                    ? $log->response_time . ' ms'
                                    : '-' }}
                            </td>

                            <td>
                                {{ $log->status === 'online' ? '0%' : '100%' }}
                            </td>

                            <td>
                                @if ($log->status === 'online')
                                    <span class="status online">
                                        <span></span>
                                        Online
                                    </span>
                                @else
                                    <span class="status offline">
                                        <span></span>
                                        Offline
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center;">
                                Belum ada data monitoring.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Ringkasan Hari Ini --}}
    <div class="panel">
        <div class="panel-heading">
            <h3>
                <i data-lucide="clipboard-list"></i>
                Ringkasan Hari Ini
            </h3>
        </div>

        <div class="summary-list">

            <div>
                <span>Rata-rata Latency</span>
                <strong>{{ $averageLatency }} ms</strong>
            </div>

            <div>
                <span>Rata-rata Packet Loss</span>
                <strong>{{ $packetLoss }}%</strong>
            </div>

            <div>
                <span>Uptime</span>
                <strong>{{ $uptime }}%</strong>
            </div>

            <div>
                <span>Jumlah Pengukuran</span>
                <strong>{{ number_format($totalChecks) }}</strong>
            </div>

            <div>
                <span>Total Gangguan</span>
                <strong>{{ number_format($totalIncidents) }}</strong>
            </div>

        </div>
    </div>

</div>

@endsection