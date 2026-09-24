@extends('layouts.app')
@section('title', 'Gangguan')
@section('content')
<div class="page-heading"><div><div class="breadcrumb">Home / Gangguan</div><h1>Gangguan</h1><p>Daftar gangguan koneksi yang terdeteksi oleh sistem.</p></div></div>
<div class="panel"><div class="panel-heading"><h3><i data-lucide="triangle-alert"></i>Riwayat Gangguan</h3><span class="badge">1 gangguan</span></div><div class="table-wrap"><table><thead><tr><th>No</th><th>Waktu Mulai</th><th>Waktu Selesai</th><th>Target</th><th>Keterangan</th><th>Status</th></tr></thead><tbody><tr><td>1</td><td>24 Sep 2026, 14:22</td><td>24 Sep 2026, 14:24</td><td>192.168.1.1</td><td>Packet loss tinggi</td><td><span class="status online"><span></span>Selesai</span></td></tr></tbody></table></div></div>
@endsection
