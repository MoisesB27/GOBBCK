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
            $table->string('cedula');
            $table->string('direccion');
            $table->string('telefono');
            $table->boolean('discapacidad')->nullable();

            // Relaciones
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('institucion_id');
            $table->unsignedBigInteger('pgob_id')->nullable();
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('tramite_id')->nullable();


            $table->enum('tipo_beneficiario', ['para_mi', 'otra_persona', 'menor'])->default('para_mi');


            // Fecha y hora de la cita
            $table->date('fecha_cita');
            $table->time('hora_cita');

            $table->timestamps();

            // Claves foráneas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tramite_id')->references('id')->on('tramites')->onDelete('cascade');
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
