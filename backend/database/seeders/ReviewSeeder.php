<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Aquí solo la lógica de reseñas
        Review::factory(50)->create();
    }
}