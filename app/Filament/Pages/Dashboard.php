<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function mount(): void
    {
        if (\Filament\Facades\Filament::getCurrentPanel()?->getId() === 'portal') {
            $this->redirectRoute('portal.dashboard');
        }
    }

    public static function getNavigationUrl(): string
    {
        return \Filament\Facades\Filament::getCurrentPanel()?->getId() === 'portal'
            ? route('portal.dashboard')
            : parent::getNavigationUrl();
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('portal')
                ->label('Tampilan Portal')
                ->icon('heroicon-o-squares-2x2')
                ->url(route('portal.dashboard')),
        ];
    }

    protected static bool $isLazy = false;

    // canView() dihapus — sekarang semua role (User, Superuser, Admin,
    // Superadmin) bisa mengakses Dashboard. Default Filament sudah
    // mengizinkan semua authenticated user, jadi tidak perlu di-override.

    // getHeaderWidgets() dihapus — dengan begini halaman ini otomatis
    // memakai daftar widget dari AdminPanelProvider->widgets([...]),
    // bukan lagi hardcode ke UserStats::class saja.
}
