<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if(app()->environment('production', 'staging')) {
            URL::forceScheme('https');
        }

        Filament::serving(function (): void {
            Filament::registerTheme(mix('css/app.css'));
        });
    }
}
