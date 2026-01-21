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
    Schema::create('profiles', function (Blueprint $table) {
        $table->id();
        
        // Relación con el usuario (si borras usuario, se borra perfil)
        $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();

        // Solo guardamos Apellido y Teléfono
        $table->string('surname')->nullable(); 
        $table->string('phone')->nullable();   

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Asegúrate de que coincida con el nombre en Schema::create
        Schema::dropIfExists('profiles'); 
    }
};