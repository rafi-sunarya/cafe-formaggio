@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')

<div class="page-heading users-page-heading">
    <div>
        <div class="breadcrumb">Home / Pengguna</div>
        <h1>Pengguna</h1>
        <p>Kelola akun yang dapat mengakses aplikasi.</p>
    </div>

    <a href="{{ route('users.create') }}" class="btn-add-user">
        <span class="btn-add-icon">+</span>
        <span>Tambah Pengguna</span>
    </a>
</div>

@if (session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="alert-error">
        {{ session('error') }}
    </div>
@endif

<div class="panel">
    <div class="table-wrap">
        <table class="users-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $index => $user)
                    <tr>
                        <td>{{ $index + 1 }}</td>

                        <td>{{ $user->name }}</td>

                        <td>{{ $user->email }}</td>

                        <td>{{ ucfirst($user->role) }}</td>

                        <td>
                            <span class="status online">
                                <span></span>
                                Aktif
                            </span>
                        </td>

                        <td>
                            <div class="user-actions">
                                <a
                                    href="{{ route('users.edit', $user->id) }}"
                                    class="user-action-btn edit"
                                    title="Edit pengguna"
                                >
                                    <i data-lucide="pencil"></i>
                                    <span>Edit</span>
                                </a>

                                @if ($user->id !== auth()->id())
                                    <form
                                        action="{{ route('users.destroy', $user->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="user-action-btn delete"
                                            title="Hapus pengguna"
                                        >
                                            <i data-lucide="trash-2"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                @else
                                    <span class="current-user-label">
                                        Akun kamu
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="users-empty">
                            Belum ada pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection