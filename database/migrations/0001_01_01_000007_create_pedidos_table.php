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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('impuesto', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2)->default(0.00);
            $table->enum('estado', ['pendiente', 'en_proceso', 'completado', 'cancelado'])->default('pendiente');
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia'])->nullable();
            $table->string('notas', 500)->nullable();
            $table->timestamps();
        });

        Schema::create('detalle_pedidos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('restrict');
            $table->integer('cantidad')->unsigned();
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_pedidos');
        Schema::dropIfExists('pedidos');
    }
};
