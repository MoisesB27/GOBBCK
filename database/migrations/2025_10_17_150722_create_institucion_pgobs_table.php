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
        Schema::create('institucion_pgob', function (Blueprint $table) {
            $table->foreignId('institucion_id')->constrained('instituciones')->onDelete('cascade');
            $table->foreignId('pgob_id')->constrained('pgobs')->onDelete('cascade');
            $table->primary(['institucion_id', 'pgob_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('institucion_pgobs');
    }
};
