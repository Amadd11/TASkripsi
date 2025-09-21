<?php

namespace App\Providers;

use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\URL;

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
        // if (config('app.env') === 'local') {
        //     URL::forceScheme('https');
        // }
    }
}
