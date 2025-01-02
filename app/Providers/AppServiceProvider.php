<?php

namespace App\Providers;

use App\Models\KategoriBarang;
use App\Models\Satuan;
use App\Models\Unit;
use App\Observers\KategoriObserver;
use App\Observers\SatuanObserver;
use App\Observers\UnitObserver;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('superadmin') ? true : null;
        });

        Blade::directive(
            'currency',
            fn($expression) => "<?php echo 'Rp. ' . number_format($expression, 0, ',', '.'); ?>"
        );

        Unit::observe(UnitObserver::class);
        KategoriBarang::observe(KategoriObserver::class);
        Satuan::observe(SatuanObserver::class);
    }
}
