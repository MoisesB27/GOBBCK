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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // --- CAMPOS DE REFERENCIA (Las FKs del backoffice) ---

            // 1. Asignación a un Usuario (Mantenido de tu original)
            $table->foreignId('assigned_to')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null')
                    ->comment('Usuario (admin/empleado) asignado a la cita.');

            // 2. Institución y Servicio (Mantenido de tu original)
            $table->foreignId('institucion_id')->constrained('instituciones')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');

            // 3. Punto GOB (¡Necesario para filtrar en el backoffice!)
            $table->foreignId('pgob_id')
                    ->constrained('pgobs') // Asumiendo que esta es tu tabla de Puntos GOB
                    ->onDelete('cascade')
                    ->comment('Punto GOB donde se agendó la cita.');

            // 4. Estado de la Cita (Usando tu nueva tabla de referencia)
            $table->foreignId('status_id')
                    ->constrained('appointment_statuses') // Usando tu tabla 2025_10_13_030221...
                    ->default(1)
                    ->comment('Estado de la cita (Activa, Procesada, Cancelada).');


            // --- CAMPOS DE TIEMPO Y CONTROL (Mantenido de tu original) ---

            $table->date('date');
            $table->time('time');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->timestamps();

            // NOTA: Se eliminan las declaraciones redundantes de $table->foreign() y $table->unsignedBigInteger().
            // El método foreignId() ya hace todo esto por ti.

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
