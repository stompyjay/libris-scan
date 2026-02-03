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
        // IMPORTANTE: El nombre por convención de Laravel es orden alfabético
        // Author viene antes que Book -> 'author_book'
        //N:M
        Schema::create('author_book', function (Blueprint $table) {
            $table->id();

            // 1. Clave Foránea del LIBRO
            // Si borras el libro, se borra esta relación automáticamente (cascade)
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            // 2. Clave Foránea del AUTOR
            // Si borras el autor, se borra esta relación automáticamente (cascade)
            $table->foreignId('author_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            // OPCIONAL (Recomendado): Evitar duplicados
            // Esto impide que asocies el mismo autor al mismo libro 2 veces por error
            $table->unique(['book_id', 'author_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_book');
    }
};