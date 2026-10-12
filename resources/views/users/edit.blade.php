@extends('layouts.app')

@section('title', 'Edit Pengguna - Cafe Formaggio')

@section('content')
<div class="user-form-page">

    {{-- Header halaman --}}
    <div class="user-form-heading">
        <div>
            <div class="breadcrumb">Home / Pengguna / Edit</div>
            <p class="user-form-eyebrow">MANAJEMEN PENGGUNA</p>
            <h1>Edit Pengguna</h1>
            <p class="user-form-subtitle">
                Perbarui informasi akun {{ $user->name }}.
            </p>
        </div>

        <a href="{{ route('users.index') }}" class="user-back-button">
            <i data-lucide="arrow-left"></i>
            <span>Kembali ke Pengguna</span>
        </a>
    </div>

    {{-- Card form --}}
    <div class="user-form-card">

        <div class="user-form-card-header">
            <div class="user-form-heading-icon">
                <i data-lucide="user-round-pen"></i>
            </div>

            <div>
                <h2>Informasi Akun</h2>
                <p>Ubah data yang diperlukan, lalu simpan perubahan.</p>
            </div>
        </div>

        <div class="user-form-card-body">

            @if ($errors->any())
                <div class="user-form-alert error">
                    <i data-lucide="circle-alert"></i>
                    <div>
                        <strong>Data belum valid.</strong>
                        <p>Periksa kembali kolom yang ditandai.</p>
                    </div>
                </div>
            @endif

            <form
                action="{{ route('users.update', $user->id) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="user-form-grid">

                    {{-- Nama --}}
                    <div class="user-form-field full">
                        <label for="name">Nama Lengkap <span>*</span></label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Contoh: Budi Santoso"
                            autocomplete="name"
                            required
                            class="@error('name') is-invalid @enderror"
                        >

                        @error('name')
                            <small class="user-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="user-form-field full">
                        <label for="email">Alamat Email <span>*</span></label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="nama@formaggio.test"
                            autocomplete="email"
                            required
                            class="@error('email') is-invalid @enderror"
                        >

                        @error('email')
                            <small class="user-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Password baru --}}
                    <div class="user-form-field">
                        <label for="password">Password Baru</label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Kosongkan jika tidak diganti"
                            autocomplete="new-password"
                            class="@error('password') is-invalid @enderror"
                        >

                        <small class="user-form-help">
                            Minimal 8 karakter jika ingin mengganti password.
                        </small>

                        @error('password')
                            <small class="user-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Konfirmasi password --}}
                    <div class="user-form-field">
                        <label for="password_confirmation">
                            Konfirmasi Password Baru
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password baru"
                            autocomplete="new-password"
                        >
                    </div>

                    {{-- Role --}}
                    <div class="user-form-field full">
                        <label for="role">Peran Pengguna <span>*</span></label>

                        <select
                            id="role"
                            name="role"
                            required
                            class="@error('role') is-invalid @enderror"
                        >
                            <option value="admin"
                                @selected(old('role', $user->role) === 'admin')>
                                Admin
                            </option>

                            <option value="teknisi"
                                @selected(old('role', $user->role) === 'teknisi')>
                                Teknisi
                            </option>

                            <option value="pemilik"
                                @selected(old('role', $user->role) === 'pemilik')>
                                Pemilik
                            </option>
                        </select>

                        <small class="user-form-help">
                            Hak akses menu mengikuti peran yang dipilih.
                        </small>

                        @error('role')
                            <small class="user-form-error">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                {{-- Tombol --}}
                <div class="user-form-footer">
                    <a
                        href="{{ route('users.index') }}"
                        class="user-form-button secondary"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="user-form-button primary"
                    >
                        <i data-lucide="save"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection