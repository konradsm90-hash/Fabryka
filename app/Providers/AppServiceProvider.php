<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('zarzadzaj-produkcja', function ($user) {
            return in_array($user->rola ?? '', ['kierownik', 'admin']);
        });

        Gate::define('widok-pracownika', function ($user) {
            return !empty($user->pracownik_id);
        });
    }
}