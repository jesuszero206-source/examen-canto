<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->string('estado', 50)->default('pendiente')->change();
            $table->softDeletes();
        });
        
        Schema::table('reservas', function (Blueprint $table) {
            $table->renameColumn('ubicacion_deseada', 'ubicacion_preferida');
            $table->renameColumn('cantidad_personas', 'personas');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->renameColumn('ubicacion_preferida', 'ubicacion_deseada');
            $table->renameColumn('personas', 'cantidad_personas');
        });
        
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
