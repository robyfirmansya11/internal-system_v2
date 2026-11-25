<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PortalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('portal')
            ->path('portal/workspace')
            ->authGuard('web')
            ->login(fn () => redirect()->route('portal.login'))
            ->brandName('Internal System')
            ->brandLogo(fn () => view('portal.partials.brand'))
            ->brandLogoHeight('7rem')
            ->homeUrl(fn () => route('portal.dashboard'))
            ->colors(['primary' => Color::hex('#465fff')])
            ->sidebarWidth('270px')
            ->maxContentWidth(Width::Full)
            ->viteTheme('resources/css/filament/portal/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                \App\Http\Middleware\SetLocale::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class])
            ->renderHook(PanelsRenderHook::TOPBAR_START, fn () => view('portal.partials.workspace-topbar'))
            ->renderHook(PanelsRenderHook::FOOTER, fn () => view('portal.partials.workspace-footer'))
            ->renderHook(PanelsRenderHook::HEAD_END, fn () => view('portal.partials.workspace-scripts'));
    }
}
