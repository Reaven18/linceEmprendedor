<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
public function run(): void
    {
        Rol::firstOrCreate(['nombre' => 'Vendedor']);
        Rol::firstOrCreate(['nombre' => 'Cliente']);
        Rol::firstOrCreate(['nombre' => 'Admin']);

        $rolAdmin   = Rol::where('nombre', 'Admin')->first();

        User::firstOrCreate(
            ['correo' => 'admin@test.com'],
            [
                'nombre'   => 'Administrador del Sistema',
                'password' => Hash::make('admin123'),
                'telefono' => '1234567890',
                'carrera'   => 'N/A',
            ]
        )->roles()->sync($rolAdmin->id);
    }
}
