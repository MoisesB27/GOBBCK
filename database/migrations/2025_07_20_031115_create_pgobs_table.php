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
        Schema::create('pgobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('descripcion')->nullable();
            $table->json('business_hours')->nullable();
            $table->integer('appointment_limit')->default(0);
            $table->integer('appointment_limit_per_user')->default(0);
            $table->boolean('is_active')->default(true)->comment('Indica si el Punto GOB está activo y recibiendo citas.');

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pgobs');
    }
};
