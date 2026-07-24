<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_reserva')->unique();
            $table->foreignId('mesa_id')->nullable()->constrained('mesas')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->date('fecha');
            $table->time('hora');
            $table->integer('cantidad_personas');
            $table->string('ubicacion_deseada')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('nombre_reserva');
            $table->string('telefono');
            $table->string('correo')->nullable();
            $table->enum('estado', ['pendiente', 'confirmada', 'finalizada', 'cancelada'])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
