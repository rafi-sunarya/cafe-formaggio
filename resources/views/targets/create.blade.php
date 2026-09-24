
@extends('layouts.app')

@section('title', 'Tambah Target')

@section('content')
<div class="page-heading">
    <div>
        <div class="breadcrumb">Home / Gateway / Target / Tambah</div>
        <h1>Tambah Target</h1>
        <p>Tambahkan alamat jaringan yang ingin dipantau.</p>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <h3>
            <i data-lucide="router"></i>
            Form Target Monitoring
        </h3>
    </div>

    <form action="{{ route('targets.store') }}" method="POST" style="padding: 24px;">
        @csrf

        <div style="margin-bottom: 18px;">
            <label for="name">Nama Target</label>
            <input
                type="text"
                id="name"
                name="name"
                placeholder="Contoh: Gateway Cafe"
                required
                style="display: block; width: 100%; max-width: 500px; padding: 10px; margin-top: 8px; border: 1px solid #ddd; border-radius: 6px;"
            >
        </div>

        <div style="margin-bottom: 18px;">
            <label for="ip_address">IP Address</label>
            <input
                type="text"
                id="ip_address"
                name="ip_address"
                placeholder="Contoh: 192.168.1.1"
                required
                style="display: block; width: 100%; max-width: 500px; padding: 10px; margin-top: 8px; border: 1px solid #ddd; border-radius: 6px;"
            >
        </div>

        <div style="margin-bottom: 18px;">
            <label for="description">Deskripsi</label>
            <textarea
                id="description"
                name="description"
                placeholder="Keterangan gateway (opsional)"
                rows="4"
                style="display: block; width: 100%; max-width: 500px; padding: 10px; margin-top: 8px; border: 1px solid #ddd; border-radius: 6px;"
            ></textarea>
        </div>

        <div style="margin-bottom: 24px;">
            <label>
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    checked
                >
                Aktifkan monitoring target
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i>
                Simpan Target
            </button>

            <a href="{{ route('targets.index') }}" class="btn">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection