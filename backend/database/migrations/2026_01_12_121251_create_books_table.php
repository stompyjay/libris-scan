<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();

            // 1. Relaciones (Claves foráneas)
            // Relaciona el libro con el usuario que lo creó
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // CUMPLE REQUISITO 1:N (Categoría - Libro)
            // Si borras la categoría, el libro se queda pero con category_id = null
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');

            // 2. Información del Libro
            $table->string('title');
            
            // --- AQUÍ BORRAMOS 'author' ---
            // Los autores irán en la tabla 'authors' y se unirán con la tabla 'author_book'.
            $table->string('author')->default('Desconocido');
            $table->string('isbn')->nullable(); // Recomendado añadir ISBN
            $table->string('cover_id')->nullable(); // ID de OpenLibrary
            $table->text('description')->nullable(); 
            
            // 3. Estado del libro
            $table->enum('status', ['pending', 'reading', 'completed', 'borrowed'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};