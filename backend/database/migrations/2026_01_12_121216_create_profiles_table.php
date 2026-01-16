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
            
            // Muy bien el uso de unique() para garantizar integridad
            // y permitir el uso seguro de firstOrCreate.
            $table->string('name')->unique(); 
            
            $table->text('description')->nullable(); 
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