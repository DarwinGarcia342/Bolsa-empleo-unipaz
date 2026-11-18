<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // <--- ESTA LÍNEA ES LA QUE FALTA
use App\Models\Company;

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
        // 1. Forzar HTTPS en producción
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // 2. Inyectar $pendingCompanies globalmente en la barra lateral y vistas admin
        View::composer(['layouts.app', 'admin.*'], function ($view) {
            // Verificamos si la tabla existe para evitar errores en migraciones limpias
            $view->with('pendingCompanies', Company::where('status', 'pending')->get());
        });
    }
}
