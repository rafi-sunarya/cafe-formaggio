@extends('layouts.app')
@section('title', 'Pengaturan')
@section('content')
<div class="page-heading"><div><div class="breadcrumb">Home / Pengaturan</div><h1>Pengaturan</h1><p>Konfigurasi dasar aplikasi monitoring.</p></div></div><div class="panel form-panel"><h3>Pengaturan Monitoring</h3><label>Interval default (detik)</label><input type="number" value="60"><label>Timeout ping (ms)</label><input type="number" value="1000"><label>Jumlah paket per pengukuran</label><input type="number" value="5"><button class="btn btn-primary">Simpan Pengaturan</button></div>
@endsection
