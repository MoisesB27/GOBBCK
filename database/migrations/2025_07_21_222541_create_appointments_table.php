<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Asegúrate de que el nombre de la clase coincida con el nombre del archivo
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // --- IDENTIFICADOR ÚNICO ---
            $table->uuid('uuid')->unique()->comment('El identificador único para el QR.');

            // --- RELACIONES PRINCIPALES ---
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Usuario ciudadano que agendó.');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade')->comment('Servicio específico agendado.');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario empleado asignado para atender.');
            $table->foreignId('status_id')->constrained('appointment_statuses')->default(1) // Valor por defecto (Ej: 1 = Pendiente)
                ->comment('Estado actual de la cita.');

            // --- CAMPOS REDUNDANTES PARA BACKOFFICE ---
            $table->foreignId('institucion_id')->constrained('instituciones')->onDelete('cascade');
            $table->foreignId('pgob_id')->constrained('pgobs')->onDelete('cascade')->comment('Punto GOB de la cita.');

            // --- FECHA Y HORA (CORREGIDO) ---
            $table->dateTime('start_time')->comment('Fecha y hora de inicio.');
            $table->dateTime('end_time')->comment('Fecha y hora de fin.');

            // --- CAMPOS DEL FORMULARIO (FUSIONADOS) ---
            // Estos son los campos que antes estaban en la tabla 'formularios'
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email');
            $table->string('telefono', 20)->nullable();
            $table->string('cedula', 13)->nullable();
            $table->string('direccion')->nullable();
            $table->boolean('tiene_discapacidad')->default(false);
            $table->enum('tipo_beneficiario', ['para_mi', 'otra_persona', 'menor'])->default('para_mi');
            $table->json('datos_menor')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

