@extends('layouts.app')

@section('title', 'Edit Target')

@section('content')
<div class="page-heading">
    <div>
        <div class="breadcrumb">Home / Gateway / Target / Edit</div>
        <h1>Edit Target</h1>
        <p>Perbarui informasi alamat jaringan yang dipantau.</p>
    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <h3>
            <i data-lucide="router"></i>
            Form Edit Target
        </h3>
    </div>

    <form
        action="{{ url('/targets/' . $gateway->id) }}"
        method="POST"
        style="padding: 24px;"
    >
        @csrf
        @method('PUT')

        <div style="margin-bottom: 18px;">
            <label for="name">Nama Target</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $gateway->name) }}"
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
                value="{{ old('ip_address', $gateway->ip_address) }}"
                required
                style="display: block; width: 100%; max-width: 500px; padding: 10px; margin-top: 8px; border: 1px solid #ddd; border-radius: 6px;"
            >
        </div>

        <div style="margin-bottom: 18px;">
            <label for="description">Deskripsi</label>
            <textarea
                id="description"
                name="description"
                rows="4"
                style="display: block; width: 100%; max-width: 500px; padding: 10px; margin-top: 8px; border: 1px solid #ddd; border-radius: 6px;"
            >{{ old('description', $gateway->description) }}</textarea>
        </div>

        <div style="margin-bottom: 24px;">
            <label>
                <input
                    type="checkbox"
                    name="is_active"
                    value="1"
                    {{ old('is_active', $gateway->is_active) ? 'checked' : '' }}
                >
                Aktifkan monitoring target
            </label>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i>
                Simpan Perubahan
            </button>

            <a href="{{ route('targets.index') }}" class="btn">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection