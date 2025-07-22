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
        Schema::create('historials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipo_servicio_id')->nullable();
            $table->unsignedBigInteger('entidad_id')->nullable();
            $table->unsignedBigInteger('appointment_id')->nullable();

            // Fechas y horarios
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();

            // Estado y ticket
            $table->string('estado')->nullable(); // Ej: Activo, Procesado, Fallido
            $table->string('ticket')->nullable();
            $table->text('detalles_ticket')->nullable();

            // Perfil / usuario
            $table->unsignedBigInteger('user_id');
            
            // Timestamps
            $table->timestamps();

            // Llaves foraneas y relaciones
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->nullOnDelete();
            $table->foreign('tipo_servicio_id')->references('id')->on('services')->nullOnDelete();
            $table->foreign('entidad_id')->references('id')->on('instituciones')->nullOnDelete();

            // Índices para optimizar consultas
            $table->index(['user_id', 'fecha', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historials');
    }
};
