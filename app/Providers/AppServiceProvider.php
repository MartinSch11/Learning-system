<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;

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
        // Configuramos el Switcher
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            return $switch
                ->locales(['es', 'en']) // Español, Inglés
                ->visible(fn(): bool => true) // Opcional: Podés ocultarlo para ciertos usuarios
                ->circular(); // Opcional: hace que el diseño sea redondito (bandera)
        });
    }
}