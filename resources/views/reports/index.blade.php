@extends('layouts.app')

@section('title', 'Laporan - Cafe Formaggio')

@section('content')

<div class="page-heading report-heading">

    <div class="report-title">
        <div class="breadcrumb">Home / Laporan</div>

        <h1>Laporan</h1>

        <p>
            Ringkasan performa kualitas koneksi gateway berdasarkan periode monitoring.
        </p>
    </div>

    <div class="report-actions">

        <button
            type="button"
            class="btn btn-primary print-btn"
            onclick="window.print()"
        >
            <i data-lucide="printer"></i>
            <span>Cetak Laporan</span>
        </button>

        <div class="target-select">
            <label for="gateway_id">Target Monitoring</label>

            <select
                id="gateway_id"
                onchange="window.location.href=this.value"
            >
                @foreach($gateways as $gateway)
                    <option
                        value="{{ route('reports.index', [
                            'gateway_id' => $gateway->id,
                            'period' => $period
                        ]) }}"
                        {{ $selectedGatewayId == $gateway->id ? 'selected' : '' }}
                    >
                        {{ $gateway->name }} ({{ $gateway->ip_address }})
                    </option>
                @endforeach
            </select>
        </div>

    </div>

</div>


{{-- Periode --}}

<div class="panel report-period-panel">

    <div class="panel-heading">

        <h3>
            <i data-lucide="calendar-range"></i>
            Periode Laporan
        </h3>

        <div class="period-tabs">

            <a
                href="{{ route('reports.index', [
                    'gateway_id' => $selectedGatewayId,
                    'period' => '24h'
                ]) }}"
                class="{{ $period === '24h' ? 'active' : '' }}"
            >
                24 Jam
            </a>

            <a
                href="{{ route('reports.index', [
                    'gateway_id' => $selectedGatewayId,
                    'period' => '7d'
                ]) }}"
                class="{{ $period === '7d' ? 'active' : '' }}"
            >
                7 Hari
            </a>

            <a
                href="{{ route('reports.index', [
                    'gateway_id' => $selectedGatewayId,
                    'period' => '30d'
                ]) }}"
                class="{{ $period === '30d' ? 'active' : '' }}"
            >
                30 Hari
            </a>

        </div>

    </div>

    <div class="report-period-info">

        <strong>
            {{ $periodLabel }}
        </strong>

        @if ($selectedGateway)

            <span>
                Target:
                {{ $selectedGateway->name }}
                ({{ $selectedGateway->ip_address }})
            </span>

        @else

            <span>
                Target: Semua Gateway
            </span>

        @endif

    </div>

</div>


{{-- KPI --}}

<div class="kpi-grid">

    <div class="kpi-card">

        <div class="kpi-icon blue">
            <i data-lucide="activity"></i>
        </div>

        <div>
            <span>
                Rata-rata Latency
            </span>

            <strong>
                {{ $averageLatency }} ms
            </strong>

            <small>
                Berdasarkan koneksi online
            </small>
        </div>

    </div>


    <div class="kpi-card">

        <div class="kpi-icon orange">
            <i data-lucide="database"></i>
        </div>

        <div>
            <span>
                Packet Loss
            </span>

            <strong>
                {{ $packetLoss }}%
            </strong>

            <small>
                Berdasarkan sampel monitoring
            </small>
        </div>

    </div>


    <div class="kpi-card">

        <div class="kpi-icon purple">
            <i data-lucide="shield-check"></i>
        </div>

        <div>
            <span>
                Availability
            </span>

            <strong>
                {{ $uptime }}%
            </strong>

            <small>
                Ketersediaan gateway
            </small>
        </div>

    </div>


    <div class="kpi-card">

        <div class="kpi-icon green">
            <i data-lucide="list-checks"></i>
        </div>

        <div>
            <span>
                Total Pemeriksaan
            </span>

            <strong>
                {{ number_format($totalChecks) }}
            </strong>

            <small>
                {{ $onlineChecks }} online /
                {{ $offlineChecks }} offline
            </small>
        </div>

    </div>

</div>


{{-- Grafik --}}

<div class="panel">

    <div class="panel-heading">

        <h3>
            <i data-lucide="chart-line"></i>
            Grafik Latency
        </h3>

        <span class="badge">
            {{ $periodLabel }}
        </span>

    </div>

    @if ($chartData->count() > 0)

        <div class="report-chart">

            <svg
                viewBox="0 0 900 260"
                preserveAspectRatio="none"
            >

                <g class="grid-lines">

                    <line
                        x1="0"
                        y1="30"
                        x2="900"
                        y2="30"
                    />

                    <line
                        x1="0"
                        y1="90"
                        x2="900"
                        y2="90"
                    />

                    <line
                        x1="0"
                        y1="150"
                        x2="900"
                        y2="150"
                    />

                    <line
                        x1="0"
                        y1="210"
                        x2="900"
                        y2="210"
                    />

                </g>

                @php

                    $chartCount = $chartData->count();

                    $maxLatency = max(
                        100,
                        (float) (
                            $chartData
                                ->whereNotNull('latency')
                                ->max('latency') ?? 100
                        )
                    );

                    $points = [];

                    foreach ($chartData as $index => $item) {

                        if ($chartCount > 1) {

                            $x = (
                                $index /
                                ($chartCount - 1)
                            ) * 900;

                        } else {

                            $x = 450;

                        }

                        $latency = $item['latency'] ?? 0;

                        $y = 210 - (
                            ($latency / $maxLatency) * 180
                        );

                        $y = max(
                            30,
                            min(210, $y)
                        );

                        $points[] =
                            round($x, 2) . ',' .
                            round($y, 2);

                    }

                @endphp

                <polyline
                    class="line"
                    points="{{ implode(' ', $points) }}"
                />

            </svg>

        </div>

    @else

        <div class="empty-state">

            <i data-lucide="chart-no-axes-combined"></i>

            <strong>
                Belum ada data monitoring
            </strong>

            <span>
                Data grafik akan muncul setelah monitoring berjalan.
            </span>

        </div>

    @endif

</div>


{{-- Ringkasan --}}

<div class="bottom-grid report-bottom-grid">

    <div class="panel">

        <div class="panel-heading">

            <h3>
                <i data-lucide="table-2"></i>
                Data Monitoring
            </h3>

            <span class="badge">
                {{ $logs->count() }} data
            </span>

        </div>

        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>
                            No
                        </th>

                        <th>
                            Waktu
                        </th>

                        <th>
                            Target
                        </th>

                        <th>
                            Latency
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($logs->take(20) as $index => $log)

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $log->checked_at->format('d M Y, H:i:s') }}
                            </td>

                            <td>
                                {{ $log->gateway?->name ?? '-' }}
                            </td>

                            <td>

                                @if ($log->response_time !== null)

                                    {{ $log->response_time }} ms

                                @else

                                    -

                                @endif

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

                            <td
                                colspan="5"
                                style="text-align:center;padding:30px;"
                            >
                                Belum ada data monitoring.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    {{-- Ringkasan --}}

    <div class="panel">

        <div class="panel-heading">

            <h3>
                <i data-lucide="clipboard-list"></i>
                Ringkasan Laporan
            </h3>

        </div>

        <div class="summary-list">

            <div>

                <span>
                    Periode
                </span>

                <strong>
                    {{ $periodLabel }}
                </strong>

            </div>

            <div>

                <span>
                    Total Pemeriksaan
                </span>

                <strong>
                    {{ number_format($totalChecks) }}
                </strong>

            </div>

            <div>

                <span>
                    Pemeriksaan Online
                </span>

                <strong class="green-text">
                    {{ number_format($onlineChecks) }}
                </strong>

            </div>

            <div>

                <span>
                    Pemeriksaan Offline
                </span>

                <strong style="color:var(--red);">
                    {{ number_format($offlineChecks) }}
                </strong>

            </div>

            <div>

                <span>
                    Rata-rata Latency
                </span>

                <strong>
                    {{ $averageLatency }} ms
                </strong>

            </div>

            <div>

                <span>
                    Packet Loss
                </span>

                <strong>
                    {{ $packetLoss }}%
                </strong>

            </div>

            <div>

                <span>
                    Availability
                </span>

                <strong>
                    {{ $uptime }}%
                </strong>

            </div>

        </div>

    </div>

</div>

@endsection