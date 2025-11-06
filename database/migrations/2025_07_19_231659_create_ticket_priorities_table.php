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
        // Esta tabla guarda los tipos de prioridad (Urgente, Prioritaria, Ordinaria)
        Schema::create('ticket_priorities', function (Blueprint $table) {
            $table->id();

            // 'name' es el nombre de la prioridad (ej. "Urgente")
            $table->string('name')->unique();

            // 'color_code' es para el color en el dashboard (ej. "#dc3545" para rojo)
            $table->string('color_code')->default('#6c757d');

            // 'description' es una explicación interna para el admin
            $table->text('description')->nullable();

            // 'is_active' permite al admin desactivar prioridades si ya no se usan
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_priorities');
    }
};
