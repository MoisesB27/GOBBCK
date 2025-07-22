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
        Schema::create('ubicacions', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la sede o sucursal
            $table->string('tipo')->nullable(); // Sucursal principal, móvil, etc.
            $table->decimal('latitude', 10, 7); // Coordenada precisa
            $table->decimal('longitude', 10, 7); // Coordenada precisa
            $table->string('address')->nullable(); // Dirección legible para mostrar
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable(); // Código postal (opcional)
            $table->string('contacto')->nullable(); // Teléfono o mail de contacto
            $table->integer('radio_cobertura')->default(10); // Radio de cobertura en km
            $table->json('extras')->nullable(); // Para comentarios, horarios, etc.
            $table->unsignedBigInteger('pgob_id');
            $table->timestamps();

            $table->foreign('pgob_id')
                ->references('id')
                ->on('pgobs')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubicacions');
    }
};
