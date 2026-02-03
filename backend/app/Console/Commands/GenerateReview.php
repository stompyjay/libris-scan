<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Review;

class GenerateReview extends Command
{
    // El nombre para llamar al comando
    protected $signature = 'review:generate'; 
    protected $description = 'Genera una reseña automática aleatoria';

    public function handle()
    {
        // Usamos el Factory para crear UNA sola reseña
        Review::factory()->create();
        $this->info('Reseña automática generada correctamente.');
    }
}