@extends('layouts.app')

@section('content')
<style>
    .user-create-page {
        padding: 28px;
        color: #38291f;
    }

    .user-create-page * {
        box-sizing: border-box;
    }

    .user-create-page .page-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 26px;
    }

    .user-create-page .eyebrow {
        color: #9b7657;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        margin: 0 0 8px;
    }

    .user-create-page h1 {
        margin: 0;
        font-size: clamp(24px, 3vw, 30px);
        font-weight: 750;
        letter-spacing: -0.8px;
    }

    .user-create-page .subtitle {
        margin: 9px 0 0;
        color: #897b70;
        font-size: 14px;
        line-height: 1.6;
    }

    .user-create-page .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 11px 16px;
        border: 1px solid #e7dace;
        border-radius: 10px;
        background: #fffdf9;
        color: #604832;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        transition: .2s ease;
    }

    .user-create-page .back-button:hover {
        background: #f4eadf;
        border-color: #cdb69f;
    }

    .user-create-page .form-card {
        max-width: 850px;
        background: #fff;
        border: 1px solid #eee5db;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(76, 49, 27, .045);
    }

    .user-create-page .card-heading {
        padding: 22px 26px;
        border-bottom: 1px solid #f0e8df;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .user-create-page .heading-icon {
        width: 46px;
        height: 46px;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background: #f4e8d9;
        color: #795638;
        font-size: 21px;
    }

    .user-create-page .card-heading h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }

    .user-create-page .card-heading p {
        margin: 5px 0 0;
        color: #95867a;
        font-size: 12px;
    }

    .user-create-page .form-body {
        padding: 26px;
    }

    .user-create-page .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 22px;
    }

    .user-create-page .field {
        min-width: 0;
    }

    .user-create-page .field.full {
        grid-column: 1 / -1;
    }

    .user-create-page .field label {
        display: block;
        margin-bottom: 9px;
        font-size: 13px;
        font-weight: 650;
        color: #49372a;
    }

    .user-create-page .required {
        color: #b65d48;
    }

    .user-create-page .field input,
    .user-create-page .field select {
        width: 100%;
        min-height: 46px;
        padding: 11px 13px;
        border: 1px solid #e6dcd1;
        border-radius: 10px;
        background: #fffdfb;
        color: #38291f;
        font: inherit;
        font-size: 13px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .user-create-page .field input:focus,
    .user-create-page .field select:focus {
        border-color: #a98260;
        box-shadow: 0 0 0 3px rgba(169, 130, 96, .13);
    }

    .user-create-page .field input::placeholder {
        color: #b5a89c;
    }

    .user-create-page .helper {
        margin: 8px 0 0;
        font-size: 11px;
        color: #96877a;
        line-height: 1.5;
    }

    .user-create-page .error {
        margin-top: 7px;
        color: #b42318;
        font-size: 12px;
    }

    .user-create-page .alert-error {
        margin-bottom: 20px;
        padding: 13px 15px;
        border: 1px solid #f1c7c2;
        border-radius: 10px;
        background: #fff3f1;
        color: #a83228;
        font-size: 13px;
    }

    .user-create-page .form-footer {
        display: flex;
        justify-content: flex-end;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 28px;
        padding-top: 22px;
        border-top: 1px solid #f0e8df;
    }

    .user-create-page .button {
        min-height: 43px;
        padding: 11px 19px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 650;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .user-create-page .button-cancel {
        border: 1px solid #e6dcd1;
        background: #fff;
        color: #6f5a48;
    }

    .user-create-page .button-save {
        border: 1px solid #65462f;
        background: #65462f;
        color: white;
    }

    .user-create-page .button-save:hover {
        background: #4e3525;
        transform: translateY(-1px);
    }

    .user-create-page .button-cancel:hover {
        background: #f8f1e9;
    }

    @media (max-width: 600px) {
        .user-create-page {
            padding: 18px 14px;
        }

        .user-create-page .page-heading {
            align-items: flex-start;
        }

        .user-create-page .form-grid {
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .user-create-page .field.full {
            grid-column: auto;
        }

        .user-create-page .card-heading,
        .user-create-page .form-body {
            padding: 19px;
        }

        .user-create-page .form-footer {
            flex-direction: column-reverse;
        }

        .user-create-page .button {
            width: 100%;
        }
    }
</style>

<div class="user-create-page">

    <div class="page-heading">
        <div>
            <p class="eyebrow">Manajemen Pengguna</p>
            <h1>Tambah Pengguna</h1>
            <p class="subtitle">
                Tambahkan akun baru untuk mengakses sistem monitoring Cafe Formaggio.
            </p>
        </div>

        <a href="{{ route('users.index') }}" class="back-button">
            ← Kembali ke Pengguna
        </a>
    </div>

    <div class="form-card">
        <div class="card-heading">
            <div class="heading-icon">♙</div>
            <div>
                <h2>Informasi Akun</h2>
                <p>Lengkapi informasi pengguna di bawah ini.</p>
            </div>
        </div>

        <div class="form-body">

            @if ($errors->any())
                <div class="alert-error">
                    <strong>Data belum valid.</strong>
                    Periksa kembali isian formulir kamu.
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-grid">

                    <div class="field full">
                        <label for="name">
                            Nama Lengkap <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Budi Santoso"
                            autocomplete="name"
                            required
                        >
                        @error('name')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field full">
                        <label for="email">
                            Alamat Email <span class="required">*</span>
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="nama@formaggio.test"
                            autocomplete="email"
                            required
                        >
                        @error('email')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password">
                            Password <span class="required">*</span>
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            autocomplete="new-password"
                            required
                        >
                        <p class="helper">Gunakan password yang kuat.</p>
                        @error('password')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="password_confirmation">
                            Konfirmasi Password <span class="required">*</span>
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <div class="field full">
                        <label for="role">
                            Peran Pengguna <span class="required">*</span>
                        </label>
                        <select id="role" name="role" required>
                            <option value="">Pilih peran pengguna</option>
                            <option value="admin" @selected(old('role') === 'admin')>
                                Admin
                            </option>
                            <option value="teknisi" @selected(old('role') === 'teknisi')>
                                Teknisi
                            </option>
                            <option value="pemilik" @selected(old('role') === 'pemilik')>
                                Pemilik
                            </option>
                        </select>
                        <p class="helper">
                            Hak akses menu akan mengikuti peran yang dipilih.
                        </p>
                        @error('role')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="form-footer">
                    <a href="{{ route('users.index') }}"
                       class="button button-cancel">
                        Batal
                    </a>

                    <button type="submit" class="button button-save">
                        + Simpan Pengguna
                    </button>
                </div>
            </form>

        </div>
    </div>

</div>
@endsection