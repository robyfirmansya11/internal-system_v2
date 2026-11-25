<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal · Internal System</title>
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <script src="{{ asset('js/portal.js') }}" defer></script>
</head>
<body>
<a class="skip" href="#main">{{ __('Lewati navigasi') }}</a>
<aside id="sidebar" class="sidebar">
    <a class="brand" href="{{ route('portal.dashboard') }}"><img class="portal-brand-logo" src="{{ asset('Logo_InSys.png') }}" alt="Internal System"></a>
    <nav aria-label="{{ __('Menu utama') }}">
        @foreach($navigationGroups as $group)
            <details class="nav-group" open>
                <summary>{{ __($group['label']) }}</summary>
                <ul class="nav-items">@include('portal.partials.navigation-items', ['items' => $group['items']])</ul>
            </details>
        @endforeach
    </nav>
    <div class="sidebar-bottom"><strong>{{ __('Satu akun, dua tampilan.') }}</strong><p>{{ __('Kelola aktivitas Anda melalui menu portal. Panel lama juga tetap tersedia.') }}</p><a class="button secondary" href="{{ route('filament.admin.pages.dashboard') }}">{{ __('Buka Filament') }}</a><form method="POST" action="{{ route('portal.logout') }}" style="margin-top:12px">@csrf<button class="button secondary" type="submit">{{ __('Keluar') }}</button></form></div>
</aside>
<div class="workspace">
    <header class="topbar">
        @include('portal.partials.language-switcher')
        <button class="icon-button menu-toggle" id="menu-toggle" aria-label="{{ __('Buka navigasi') }}" aria-controls="sidebar" aria-expanded="false">☰</button>
        <span class="breadcrumb">{{ __('Workspace') }} <span>/</span> <strong>{{ __('Dashboard') }}</strong></span>
        <div class="account"><button class="icon-button" id="theme-toggle" aria-label="{{ __('Ganti tema') }}" aria-pressed="false">◐</button><span class="avatar">{{ mb_substr($user->name, 0, 1) }}</span><div><strong>{{ $user->name }}</strong><small>{{ __($user->level?->label()) }}</small></div></div>
    </header>
    <main id="main">
        <div class="page-heading"><div><p class="eyebrow">{{ __('EMPLOYEE PORTAL') }}</p><h1>{{ __('Dashboard') }}</h1><p>{{ __('Selamat datang, :name. Berikut ringkasan pengajuan Anda.', ['name' => $user->name]) }}</p></div><span class="date">{{ now()->translatedFormat('d F Y') }}</span></div>
        <section class="stats" aria-label="{{ __('Ringkasan pengajuan pribadi') }}">
            @foreach($stats as $label => $value)
                <article class="card stat"><span class="stat-symbol" aria-hidden="true">@svg(['heroicon-o-document-text', 'heroicon-o-clock', 'heroicon-o-check-circle', 'heroicon-o-x-circle'][$loop->index], 'icon')</span><p>{{ __($label) }}</p><strong>{{ number_format($value) }}</strong><small>{{ __('Seluruh periode') }}</small></article>
            @endforeach
        </section>
        <div class="content-grid">
            <section class="card services"><div class="section-heading"><div><h2>{{ __('Semua menu') }}</h2><p>{{ __(':count menu tersedia sesuai hak akses Anda.', ['count' => $navigationLinks->count()]) }}</p></div></div><label class="search"><span>{{ __('Cari menu atau grup') }}</span><input id="module-search" type="search" placeholder="Attendance, Finance, Report, My Profile…" autocomplete="off"></label><div class="module-grid">
                @foreach($navigationLinks as $module)
                    <a class="module" data-module="{{ mb_strtolower($module['label'].' '.$module['group']) }}" href="{{ $module['url'] }}" @if($module['new_tab']) target="_blank" rel="noopener noreferrer" @endif><span class="module-icon">{{ \Filament\Support\generate_icon_html($module['icon'], attributes: new \Illuminate\View\ComponentAttributeBag(['class' => 'icon', 'aria-hidden' => 'true'])) }}</span><strong>{{ __($module['label']) }}</strong><span>{{ __($module['group']) }}</span></a>
                @endforeach
            </div><p id="search-empty" @if($navigationLinks->isNotEmpty()) hidden @endif role="status">{{ __('Tidak ada menu yang cocok.') }}</p></section>
            <aside class="card guide"><span class="guide-label">{{ __('RUANG KERJA ANDA') }}</span><h2>{{ __('Urus pengajuan. Pantau progresnya.') }}</h2><p>{{ __('Ringkasan ini menampilkan pengajuan milik Anda dari modul yang dapat Anda akses.') }}</p><div class="guide-note"><span class="stat-symbol">@svg('heroicon-o-shield-check', 'icon')</span><strong>{{ __('Terhubung dengan akun Anda') }}</strong><p>{{ __('Tabel, formulir, detail, dan laporan dapat dibuka langsung melalui menu portal.') }}</p></div><a class="button" href="{{ route('filament.portal.pages.my-profile') }}">{{ __('Buka profil saya') }}</a></aside>
        </div>
        <section class="card activity"><div class="section-heading"><div><h2>{{ __('Pengajuan terbaru') }}</h2><p>{{ __('Delapan pengajuan pribadi yang terakhir diperbarui.') }}</p></div><span class="badge">{{ __('Aktivitas saya') }}</span></div><div class="table-scroll"><table><thead><tr><th>{{ __('Pengajuan') }}</th><th>{{ __('Modul') }}</th><th>{{ __('Status') }}</th><th>{{ __('Diperbarui') }}</th><th><span class="sr-only">{{ __('Tindakan') }}</span></th></tr></thead><tbody>
        @forelse($recent as $item)
            <tr><td class="request-title">{{ $item['title'] }}</td><td>{{ __($item['label']) }}</td><td><span class="status {{ match($item['status']) { __('Approved') => 'approved', __('Rejected') => 'rejected', default => 'pending' } }}">{{ __($item['status']) }}</span></td><td>{{ $item['updated_at']?->format('d M Y, H:i') }}</td><td><a class="text-link" href="{{ $item['url'] }}" aria-label="Buka modul {{ __($item['label']) }}">{{ __('Buka modul') }}</a></td></tr>
        @empty
            <tr><td colspan="5" class="empty"><strong>{{ __('Belum ada pengajuan') }}</strong><p>{{ __('Mulai melalui salah satu layanan karyawan di atas.') }}</p></td></tr>
        @endforelse
        </tbody></table></div></section>
        <footer>Internal System <span>{{ __('Portal karyawan · Tampilan alternatif') }}</span></footer>
    </main>
</div>
</body>
</html>
