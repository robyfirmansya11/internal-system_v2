<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\{EmployeeFinance, EmployeeProfile, FormCuti, Kasbon, NotaPenggantianBiaya, PerjalananDinas, SuratPerintahBayar, User};
use App\Observers\SensitiveActivityObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach ([\Filament\Forms\Components\Field::class, \Filament\Tables\Columns\Column::class, \Filament\Tables\Filters\BaseFilter::class, \Filament\Actions\Action::class] as $component) {
            $component::configureUsing(fn ($instance) => $instance->translateLabel());
        }
        RateLimiter::for('api-login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip());
        });

        $observer = SensitiveActivityObserver::class;
        foreach ([User::class, EmployeeProfile::class, EmployeeFinance::class, FormCuti::class, Kasbon::class, SuratPerintahBayar::class, PerjalananDinas::class, NotaPenggantianBiaya::class] as $model) {
            $model::observe($observer);
        }
    }
}
