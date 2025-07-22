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
        Schema::create('formularios', function (Blueprint $table) {
            $table->id();

            // Datos del solicitante
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email');
            $table->string('cedula')->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono')->nullable();

            // Relaciones
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('institucion_id');
            $table->unsignedBigInteger('pgob_id');
            $table->unsignedBigInteger('appointment_id')->nullable();

            $table->string('tipo_tramite');

            // Para casos donde un mayor agenda para un menor
            $table->boolean('agenda_a_menor')->default(false);
            $table->string('nombre_menor')->nullable();
            $table->string('apellido_menor')->nullable();
            $table->string('cedula_menor')->nullable();

            // Fecha y hora de la cita
            $table->date('fecha_cita');
            $table->time('hora_cita');

            $table->timestamps();

            // Claves foráneas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('cascade');
            $table->foreign('pgob_id')->references('id')->on('pgobs')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formularios');
    }
};
