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
        Schema::create('productos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('restrict');
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 255);
            $table->string('descripcion', 1000)->nullable();
            $table->decimal('precio', 10, 2)->default(0.00);
            $table->integer('existencia')->unsigned()->default(0);
            $table->boolean('disponible')->default(true);
            $table->string('imagen', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('categoria_id');
            $table->index(['disponible', 'existencia']);
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
