<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cafe Formaggio</title><link rel="stylesheet" href="{{ asset('css/app.css') }}"><script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="login-page">
<div class="login-overlay"></div>
<div class="login-card">
    <div class="login-logo">☕</div>
    <h1>Cafe Formaggio</h1><p class="login-subtitle">Aplikasi Monitoring Kualitas Internet</p>
    <form action="{{ route('login.process') }}" method="POST" class="login-form">
    @csrf

    <label>Email</label>
    <div class="input-wrap">
        <i data-lucide="mail"></i>
        <input
            type="email"
            name="email"
            placeholder="admin@formaggio.test"
            value="{{ old('email') }}"
            required
        >
    </div>

    <label>Password</label>
    <div class="input-wrap">
        <i data-lucide="lock"></i>
        <input
            type="password"
            name="password"
            placeholder="Masukkan password"
            required
        >
    </div>

    <div class="form-row">
        <label class="checkbox-label">
            <input type="checkbox" name="remember">
            Ingat saya
        </label>

        <a href="#">Lupa password?</a>
    </div>

    @if ($errors->any())
        <div class="error-message">
            {{ $errors->first() }}
        </div>
    @endif

    <button class="btn btn-primary btn-full" type="submit">
        <i data-lucide="log-in"></i>
        Login
    </button>
</form>
    <p class="login-footer">© {{ date('Y') }} Cafe Formaggio. All rights reserved.</p>
</div>
<script>lucide.createIcons();</script>
</body></html>
