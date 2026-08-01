<?php

namespace App\Providers;

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
        // El rol Administrador siempre tiene acceso a todas las rutas del sistema,
        // incluso a permisos que se agreguen más adelante.
        Gate::before(fn ($user, string $ability) => $user->hasRole('Administrador') ? true : null);
    }
}
