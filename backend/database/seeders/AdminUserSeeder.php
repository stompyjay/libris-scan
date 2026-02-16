<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Comprobamos si ya existe para no duplicarlo
        $adminEmail = 'admin@admin.com';
        
        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Super Admin',
                'email' => $adminEmail,
                'password' => Hash::make('123'), // La contraseña es: password
                'admin' => true, // IMPORTANTE: Esto te da permisos de admin
            ]);
        }
    }
}