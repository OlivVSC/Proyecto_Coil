<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Este permiso también se comprueba al enviar datos al servidor.
        Gate::define('administrar', fn (User $usuario) => $usuario->rol === 'admin');
    }
}
