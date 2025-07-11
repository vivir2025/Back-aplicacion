<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Usuario;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'id'         => (string) Str::uuid(),
            'usuario'    => 'admin',
            'contrasena' => bcrypt('password123'),
            'correo'     => 'admin@example.com',
            'nombre'     => 'Administrador',
            'rol'        => 'admin',
            'estado'     => 'activo',
            'idsede'     => 'c3662155-5e93-11f0-8f3b-525400dd23be',  // ✅ aquí tu UUID real
        ]);
    }
}