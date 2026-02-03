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
        Schema::create('book_user', function (Blueprint $table) {
            $table->id();

            // RELACIÓN: Qué usuario tiene qué libro
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');

            // DATOS PERSONALES DE LA LECTURA
            // El estado y la valoración dependen de CADA usuario, no del libro
            $table->enum('status', ['pending', 'reading', 'completed', 'dropped'])->default('pending');
            
            $table->timestamps();
        });
    }
};