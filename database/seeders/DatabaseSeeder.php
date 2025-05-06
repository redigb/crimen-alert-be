<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;

use App\Models\Users;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Users::factory()->create([
            'name' => 'RenzoRd_Redigb',
            'email' => 'redrojo@ejemplo.com',
            'password' => Hash::make('contraseña123'), // Contraseña para el primer usuario
        ]);

        Users::factory()->create([
            'name' => 'Xander-Codex',
            'email' => 'usuario@ejemplo.com',
            'password' => Hash::make('contraseña123'), // Contraseña para el segundo usuario
        ]);
    }
}
