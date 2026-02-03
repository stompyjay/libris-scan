<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Genera un nombre aleatorio (Ej: "Juan Pérez")
            'user_name' => fake()->name(), 
            
            // Genera un texto de relleno
            'content'   => fake()->paragraph(), 
            
            // Genera un número entre 1 y 5
            'rating'    => fake()->numberBetween(1, 5), 
            
            // Genera fechas aleatorias de los últimos 6 meses
            'created_at'=> fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}