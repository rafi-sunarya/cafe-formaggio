@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')

<div class="page-heading">
    <div>
        <div class="breadcrumb">Home / Pengguna</div>

        <h1>Pengguna</h1>

        <p>Kelola akun yang dapat mengakses aplikasi.</p>
    </div>

    <button class="btn btn-primary">
        <i data-lucide="plus"></i>
        Tambah Pengguna
    </button>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($users as $index => $user)
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center;">
                            Belum ada pengguna.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection