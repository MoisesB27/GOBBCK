<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('institution_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institucion_id')->constrained('instituciones')->onDelete('cascade');
            $table->enum('tipo', ['correo', 'telefono', 'whatsapp', 'otro']);
            $table->string('valor');
            $table->string('descripcion')->nullable();
            $table->boolean('principal')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('institution_contacts');
    }
};
