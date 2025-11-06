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
        Schema::create('instituciones', function (Blueprint $table) {
            $table->id(); // ID autoincremental estándar (BIGINT UNSIGNED)
            $table->string('nombre');
            $table->string('sigla')->nullable();
            $table->enum('Estado', ['Activa', 'Inactiva', 'Pendiente'])->default('Pendiente') ->comment('Estado de la institución');
            $table->string('Encargado')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instituciones');
    }
};
