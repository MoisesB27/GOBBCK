<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Nombre de clase debe coincidir con el nombre del archivo
return new class extends Migration
{
    /**
     * Run the migrations.
     * Esta migración se ejecuta DESPUÉS de que 'users' e 'instituciones' existan.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // --- AÑADIR CAMPOS DE PERFIL Y GESTIÓN ---
            // Los campos que faltan para el formulario del admin
            $table->string('apellido')->nullable()->after('name');
            $table->string('telefono')->nullable()->after('email');

            // Columna de gestión para el dashboard (LA QUE FALTABA)
            $table->boolean('is_active')->default(true)->after('password')->comment('Estado de habilitación/inhabilitación del usuario.');

            // Columna para la FK (debe ser unsignedBigInteger para la llave foránea)
            $table->unsignedBigInteger('institucion_id')->nullable()->after('telefono');

            // --- DEFINIR LA LLAVE FORÁNEA ---
            $table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Borrar la llave foránea primero
            $table->dropForeign(['institucion_id']);

            // Borrar las columnas que añadimos
            $table->dropColumn(['apellido', 'telefono', 'institucion_id', 'is_active']);
        });
    }
};
