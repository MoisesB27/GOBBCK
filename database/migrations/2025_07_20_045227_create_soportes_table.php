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
        Schema::create('soportes', function (Blueprint $table) {
            $table->id();

            // --- CAMPOS DE REPORTE DE USUARIO (TICKET) ---

            // Usuario que reporta
            $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->comment('ID del usuario logueado que reporta el incidente.');

            // Se hacen nullable por si el ticket lo envía un usuario registrado (los datos se toman del perfil)
            $table->string('nombre_completo')->nullable();
            $table->string('correo_electronico')->nullable();

            $table->string('asunto');
            $table->text('descripcion');

            // --- CAMPOS DE GESTIÓN DE BACKOFFICE (AÑADIDOS) ---

            // 1. Asignación del Ticket (Columna 'Asignado' en el backoffice)
            $table->foreignId('assigned_to_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete()
                    ->comment('ID del administrador asignado para resolver el ticket.');

            // 2. Contexto (Punto GOB afectado)
            $table->foreignId('pgob_id')
                    ->nullable()
                    ->constrained('pgobs')
                    ->nullOnDelete()
                    ->comment('ID del Punto GOB afectado por el problema reportado.');

            // 3. Estado del Ticket (Columna 'Estado')
            // Foreign Key a tu tabla de referencia ticket_statuses
            $table->foreignId('status_id')
                    ->constrained('ticket_statuses') // Usando tu tabla 2025_10_13_030537...
                    ->default(1) // Asume el ID 1 es 'Abierto'
                    ->comment('ID del estado actual del ticket.');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soportes');
    }
};
