<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mesas', function (Blueprint $table) {
            $table->id();
            $table->string('numero');
            $table->string('nombre')->nullable();
            $table->integer('capacidad');
            $table->enum('ubicacion', ['interior', 'terraza', 'balcon', 'jardin', 'ventana']);
            $table->enum('estado', ['disponible', 'reservada', 'ocupada', 'limpieza', 'fuera_de_servicio'])->default('disponible');
            $table->string('descripcion', 500)->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mesas');
    }
};
