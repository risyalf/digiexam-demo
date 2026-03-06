<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment("production")) {
            URL::forceScheme("https");
        }

        FilamentIcon::register([
            'panels::sidebar.collapse-button' => Heroicon::Bars3,
            'panels::sidebar.expand-button' => Heroicon::Bars3
        ]);
    }
}
