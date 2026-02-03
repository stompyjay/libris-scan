<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// No hace falta importar Review aquí, solo la clase del Seeder

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Primero creas usuarios o libros si hacen falta
        // ...

        // 2. AQUI LLAMAS A TU REVIEW SEEDER
        $this->call([
            ReviewSeeder::class,
            // Si tuvieras BookSeeder::class, lo pondrías aquí también
        ]);
    }
}