<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // 1. Relaciones (Claves foráneas)
            // Relaciona el libro con el usuario que lo creó
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Relaciona con la categoría (puede ser nulo si borras la categoría)
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');

            // 2. Información del Libro
            $table->string('title');
            $table->string('author')->nullable(); // A veces no hay autor detectado
            $table->string('cover_id')->nullable(); // Para guardar el ID de la foto de OpenLibrary
            $table->text('description')->nullable(); // Texto largo para sinopsis
            
            // 3. Estado del libro
            // pending = pendiente, reading = leyendo, completed = leído, borrowed = prestado
            $table->enum('status', ['pending', 'reading', 'completed', 'borrowed'])->default('pending');

            $table->timestamps();
        });
    }
};
