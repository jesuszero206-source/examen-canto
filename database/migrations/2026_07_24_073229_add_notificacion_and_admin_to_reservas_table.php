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
        Schema::table('reservas', function (Blueprint $table) {
            $table->boolean('notificado_cliente')->default(false)->after('estado');
            $table->string('motivo_estado', 500)->nullable()->after('notificado_cliente');
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete()->after('motivo_estado');
            $table->timestamp('fecha_resolucion')->nullable()->after('admin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['notificado_cliente', 'motivo_estado', 'admin_id', 'fecha_resolucion']);
        });
    }
};
