@extends('layouts.app')

@section('title', 'Riwayat Monitoring')

@section('content')

<div class="page-heading">
    <div>
        <div class="breadcrumb">Home / Riwayat Monitoring</div>
        <h1>Riwayat Monitoring</h1>
        <p>Catatan hasil pemeriksaan koneksi gateway.</p>
    </div>

    <div class="target-select">
        <label for="history_gateway_id">
            Target Monitoring
        </label>

        <form
            action="{{ route('history.index') }}"
            method="GET"
        >
            <select
                name="gateway_id"
                id="history_gateway_id"
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

<div class="panel">
    <div class="panel-heading history-panel-heading">

    <h3>
        <i data-lucide="history"></i>
        Riwayat Koneksi Gateway
    </h3>

    <div class="history-actions">
        @foreach ($gateways as $gateway)
            <form action="{{ route('history.check', $gateway) }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-primary history-check-btn">
                    <i data-lucide="refresh-cw"></i>
                    <span>Cek {{ $gateway->name }}</span>
                </button>
            </form>
        @endforeach
    </div>

</div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Gateway</th>
                    <th>IP Address</th>
                    <th>Status</th>
                    <th>Response Time</th>
                    <th>Waktu Pemeriksaan</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($logs as $index => $log)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>
                            {{ $log->gateway?->name ?? 'Gateway tidak ditemukan' }}
                        </td>

                        <td>
                            {{ $log->gateway?->ip_address ?? '-' }}
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

                        <td>
                            {{ $log->response_time !== null
                                ? $log->response_time . ' ms'
                                : '-' }}
                        </td>

                        <td>
                            {{ $log->checked_at?->format('d/m/Y H:i:s') ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">
                            Belum ada riwayat monitoring.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection