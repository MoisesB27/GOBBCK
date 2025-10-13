<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\table;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            // Asociaciones principales
            $table->unsignedBigInteger('tramite_id');
            $table->unsignedBigInteger('institucion_id')->nullable(); // Puede ser null si el servicio aplica a varios puntos gob

            // Datos del Servicio
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->integer('duration')->default(0); // En minutos
            $table->string('logo')->nullable(); // Ruta del logo o imagen

            // Ubicación y punto gob (opcional: puede relacionarse con una tabla pivote si hay muchos a muchos)
            $table->unsignedBigInteger('pgob_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable(); // Nuevo campo para el estado del servicio

            $table->string('ubicacion')->nullable(); // Lugar específico

            $table->timestamps();

            // FOREIGN KEYS
            $table->foreign('tramite_id')->references('id')->on('tramites')->onDelete('cascade');
            $table->foreign('status_id')->after('pgob_id')->references('id')->on('service_statuses')->onDelete('set null');
            $table->foreign('institucion_id')->references('id')->on('instituciones')->onDelete('set null');
            $table->foreign('pgob_id')->references('id')->on('pgobs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
