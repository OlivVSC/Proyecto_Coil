<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Las publicaciones se administran desde MySQL y no se cargan desde archivos.
        // Solo se crea un administrador cuando el propietario establece
        // ADMIN_PASSWORD en su .env privado. Nunca se publica esa clave.
        $clave = config('mars.admin_password');
        if (is_string($clave) && strlen($clave) >= 10) {
            User::updateOrCreate(
                ['email' => config('mars.admin_email')],
                ['name' => config('mars.admin_name'), 'password' => $clave, 'rol' => 'admin']
            );
        } else {
            $this->command?->warn('Administrador no creado. Define ADMIN_PASSWORD (10+ caracteres) en .env y ejecuta php artisan db:seed.');
        }
    }
}
