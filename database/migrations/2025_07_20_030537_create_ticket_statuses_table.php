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
        Schema::create('ticket_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Ej: Abierto, Cerrado, Por asignar, Escalado
            $table->string('color_code')->default('#6c757d'); // Color para el dashboard
            $table->boolean('is_active')->default(true); // Si se puede usar este estado
            $table->text('description')->nullable(); // Descripción para el admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_statuses');
    }
};

