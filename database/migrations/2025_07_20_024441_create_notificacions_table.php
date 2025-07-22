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
        Schema::create('notificacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type');
            $table->string('message');
            $table->timestamp('fecha')->useCurrent();
            $table->boolean('publico')->default(false);
            $table->enum('tipo', ['info', 'alerta', 'recomendacion'])->default('info');
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable();
            
            // Asegúrate que coincida con el tipo en services y pgobs
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('pgob_id')->nullable();
            
            $table->timestamps();

            // Claves foráneas con comprobación de existencia
            if (Schema::hasTable('users')) {
                $table->foreign('user_id')
                      ->references('id')->on('users')
                      ->onDelete('cascade');
            }

            if (Schema::hasTable('services')) {
                $table->foreign('service_id')
                      ->references('id')->on('services')
                      ->onDelete('set null');
            }

            if (Schema::hasTable('pgobs')) {
                $table->foreign('pgob_id')
                      ->references('id')->on('pgobs')
                      ->onDelete('set null');
            }

            $table->index(['user_id', 'is_read']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacions');
    }
};