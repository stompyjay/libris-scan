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

            // CUMPLE REQUISITO 1:N (Categoría - Libro)
            // Si borras la categoría, el libro se queda pero con category_id = null
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');

            // 2. Información del Libro
            $table->string('title');
            
            // --- AQUÍ BORRAMOS 'author' ---
            // Los autores irán en la tabla 'authors' y se unirán con la tabla 'author_book'.
            $table->string('author')->default('Desconocido');
            $table->string('isbn')->nullable(); // Recomendado añadir ISBN
            $table->string('cover')->nullable(); // ID de OpenLibrary
            $table->text('description')->nullable(); 
            $table->double('price')->nullable();
            

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};