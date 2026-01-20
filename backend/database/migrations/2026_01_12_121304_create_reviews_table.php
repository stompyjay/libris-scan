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
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->string('user_name'); // Nombre de quien deja la reseña
        $table->text('content');     // El texto de la opinión
        $table->integer('rating');   // 1 a 5 estrellas
        $table->boolean('is_visible')->default(true); // Para ocultarlas si quieres
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
