<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $register ? __('Daftar') : __('Masuk') }} · Internal System</title>
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal-auth.css') }}">
    <script src="{{ asset('js/portal-auth.js') }}" defer></script>
</head>
<body class="auth-page">
    <main class="auth-layout">
        <section class="auth-form-panel" aria-labelledby="auth-title">
            @include('portal.partials.language-switcher')
            <a class="auth-back" href="{{ route('filament.admin.auth.login') }}">{{ __('Panel administrasi Filament') }}</a>
            <div class="auth-form-wrap">
                <a class="brand auth-mobile-brand" href="{{ route('portal.login') }}"><img class="portal-brand-logo" src="{{ asset('Logo_InSys.png') }}" alt="Internal System"></a>
                <p class="auth-eyebrow">{{ __('EMPLOYEE WORKSPACE') }}</p>
                <h1 id="auth-title">{{ $register ? __('Buat akun Anda') : __('Selamat datang kembali') }}</h1>
                <p class="auth-description">{{ $register ? __('Daftar untuk mengakses layanan karyawan dalam satu tempat.') : __('Masukkan email dan kata sandi untuk masuk ke portal.') }}</p>
                @if($errors->any())
                    <div class="auth-errors" role="alert"><strong>{{ __('Periksa kembali data Anda.') }}</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <form method="POST" action="{{ route($register ? 'portal.register.store' : 'portal.login.store') }}">
                    @csrf
                    @if($register)
                        <div class="auth-field"><label for="name">{{ __('Nama lengkap') }} <span>*</span></label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" placeholder="{{ __('Nama lengkap Anda') }}" maxlength="255" required @error('name') aria-invalid="true" @enderror></div>
                    @endif
                    <div class="auth-field"><label for="email">{{ __('Email') }} <span>*</span></label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" placeholder="name@example.com" maxlength="255" required @error('email') aria-invalid="true" @enderror></div>
                    <div class="auth-field"><label for="password">{{ __('Kata sandi') }} <span>*</span></label><div class="password-wrap"><input id="password" type="password" name="password" autocomplete="{{ $register ? 'new-password' : 'current-password' }}" placeholder="{{ $register ? __('Buat kata sandi') : __('Masukkan kata sandi') }}" @if($register) minlength="8" aria-describedby="password-hint" @endif required @error('password') aria-invalid="true" @enderror><button type="button" data-password="password" data-show="{{ __('Tampilkan kata sandi') }}" data-hide="{{ __('Sembunyikan kata sandi') }}" aria-label="{{ __('Tampilkan kata sandi') }}" aria-pressed="false">@svg('heroicon-o-eye', 'icon')</button></div>@if($register)<small id="password-hint">{{ __('Minimal 8 karakter, termasuk huruf dan angka.') }}</small>@endif</div>
                    @if($register)
                        <div class="auth-field"><label for="password_confirmation">{{ __('Konfirmasi kata sandi') }} <span>*</span></label><div class="password-wrap"><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="{{ __('Ulangi kata sandi') }}" minlength="8" required><button type="button" data-password="password_confirmation" data-show="{{ __('Tampilkan konfirmasi kata sandi') }}" data-hide="{{ __('Sembunyikan konfirmasi kata sandi') }}" aria-label="{{ __('Tampilkan konfirmasi kata sandi') }}" aria-pressed="false">@svg('heroicon-o-eye', 'icon')</button></div></div>
                        <p class="auth-note">{{ __('Akun baru mendapat akses karyawan. Hubungi administrator untuk pengaturan departemen dan profil karyawan.') }}</p>
                    @else
                        <label class="remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> {{ __('Ingat saya') }}</label>
                    @endif
                    <button class="button auth-submit" type="submit">{{ $register ? __('Buat akun') : __('Masuk') }}</button>
                </form>
                @if($register || config('portal.registration_enabled'))
                    <p class="auth-switch">{{ $register ? __('Sudah punya akun?') : __('Belum punya akun?') }} <a href="{{ route($register ? 'portal.login' : 'portal.register') }}">{{ $register ? __('Masuk di sini') : __('Daftar sekarang') }}</a></p>
                @endif
            </div>
            <footer class="auth-footer">© {{ now()->year }} Internal System</footer>
        </section>
        <aside class="auth-brand-panel" aria-label="Internal System">
            <div class="auth-grid" aria-hidden="true"></div>
            <div class="auth-brand-content"><img class="auth-brand-logo" src="{{ asset('Logo_InSys.png') }}" alt="Internal System"><p>{{ __('Satu ruang untuk hari kerja yang lebih teratur.') }}</p><span class="auth-brand-caption">{{ __('PENGAJUAN · PERSETUJUAN · AKTIVITAS') }}</span></div>
            <span class="auth-brand-bottom">{{ __('Terhubung dengan tim. Fokus pada pekerjaan.') }}</span>
        </aside>
    </main>
    <button class="auth-theme icon-button" id="theme-toggle" type="button" aria-label="{{ __('Ganti tema') }}" aria-pressed="false">◐</button>
</body>
</html>
