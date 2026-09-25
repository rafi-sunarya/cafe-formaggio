
@extends('layouts.app')

@section('title', 'Gateway / Target')

@section('content')

@if (session('success'))
    <div style="
        background: #dcfce7;
        color: #166534;
        padding: 15px 20px;
        margin-bottom: 20px;
        border: 1px solid #86efac;
        border-radius: 8px;
    ">
        <strong>Berhasil!</strong>
        {{ session('success') }}
    </div>
@endif

@if (session('success'))
    <div style="background: #dcfce7; color: #166534; padding: 15px 20px; margin-bottom: 20px; border: 1px solid #86efac; border-radius: 8px;">
        <strong>Berhasil!</strong>
        {{ session('success') }}
    </div>
@endif

<div class="page-heading">
    <div>
        <div class="breadcrumb">Home / Gateway / Target</div>
        <h1>Gateway / Target</h1>
        <p>Kelola alamat jaringan yang ingin dipantau.</p>
    </div>

    <a href="{{ route('targets.create') }}" class="btn btn-primary">
    <i data-lucide="plus"></i>
    Tambah Target
</a>
</div>

<div class="panel">
    <div class="panel-heading">
        <h3>
            <i data-lucide="router"></i>
            Daftar Target Monitoring
        </h3>

        <input
    type="text"
    id="targetSearch"
    class="table-search"
    placeholder="Cari nama / IP address..."
>
    </div>

    <div class="table-wrap">
        <table id="targetsTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Target</th>
                    <th>IP Address</th>
                    <th>Jenis</th>
                    <th>Interval</th>
                    <th>Status Monitoring</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($gateways as $index => $gateway)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>{{ $gateway->name }}</td>

                        <td>{{ $gateway->ip_address }}</td>

                        <td>Gateway</td>

                        <td>60 detik</td>

                        <td>
    @if (!$gateway->latestConnectionLog)
        <span class="status offline">
            <span></span>
            Belum Dicek
        </span>
    @elseif ($gateway->latestConnectionLog->status === 'online')
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
                            <a
    href="{{ route('targets.edit', $gateway->id) }}"
    class="action edit"
>
    Edit
</a>

<form
    action="{{ route('history.check', $gateway->id) }}"
    method="POST"
    style="display: inline;"
>
    @csrf

    <button type="submit" class="action edit">
        Cek Sekarang
    </button>
</form>

                            <form
    action="{{ route('targets.destroy', $gateway->id) }}"
    method="POST"
    style="display: inline;"
    onsubmit="return confirm('Yakin ingin menghapus target ini?')"
>
    @csrf
    @method('DELETE')

    <button type="submit" class="action delete">
        Hapus
    </button>
</form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">
                            Belum ada gateway yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection