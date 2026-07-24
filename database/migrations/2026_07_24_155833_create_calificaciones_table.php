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
        Schema::create('calificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained()->nullOnDelete();
            $table->tinyInteger('calificacion')->unsigned();
            $table->text('comentario')->nullable();
            $table->json('etiquetas')->nullable();
            $table->enum('estado', ['visible', 'oculto', 'reportado'])->default('visible');
            $table->text('respuesta_admin')->nullable();
            
            $table->unique(['producto_id', 'user_id']);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificaciones');
    }
};
