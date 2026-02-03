<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            // Literarios
            ['name' => 'Novela'],
            ['name' => 'Cuento / Relato'],
            ['name' => 'Poesía'],
            ['name' => 'Teatro / Dramaturgia'],
            ['name' => 'Ensayo'],
            ['name' => 'Fábulas'],
            ['name' => 'Mitos y Leyendas'],
            ['name' => 'Biografías y Memorias'],
            ['name' => 'Crónica'],
            
            // Temáticas (Con Emojis si tu DB lo permite, si no, quítalos)
            ['name' => 'Infantil y Juvenil 🧸'],
            ['name' => 'Fantasía y Magia 🪄'],
            ['name' => 'Ciencia Ficción 🚀'],
            ['name' => 'Misterio y Policial 🔍'],
            ['name' => 'Terror y Sobrenatural 👻'],
            ['name' => 'Romance y Sentimientos 💗'],
            ['name' => 'Novela Histórica 🏛️'],
            ['name' => 'Filosofía y Pensamiento 🧠'],
            ['name' => 'Aventuras y Viajes 🌍'],
            ['name' => 'Humor y Sátira 😄'],
            ['name' => 'Psicológico'],

            // Formatos Especiales
            ['name' => 'Audiolibros 🎧'],
            ['name' => 'Manuales y Didáctica'],
            ['name' => 'Arte y Arquitectura'],
            ['name' => 'Gastronomía y Cocina']
        ];

        DB::table('categories')->insert($categories);
    }
}