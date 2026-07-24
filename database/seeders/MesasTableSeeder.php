<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mesa;

class MesasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $mesas = [
            // Interior
            ['numero' => 'INT-01', 'capacidad' => 2, 'ubicacion' => 'interior', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'INT-02', 'capacidad' => 4, 'ubicacion' => 'interior', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'INT-03', 'capacidad' => 4, 'ubicacion' => 'interior', 'estado' => 'ocupada', 'activa' => true],
            ['numero' => 'INT-04', 'capacidad' => 6, 'ubicacion' => 'interior', 'estado' => 'disponible', 'activa' => true],
            
            // Terraza
            ['numero' => 'TER-01', 'capacidad' => 2, 'ubicacion' => 'terraza', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'TER-02', 'capacidad' => 4, 'ubicacion' => 'terraza', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'TER-03', 'capacidad' => 8, 'ubicacion' => 'terraza', 'estado' => 'disponible', 'activa' => true],
            
            // Balcón
            ['numero' => 'BAL-01', 'capacidad' => 2, 'ubicacion' => 'balcon', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'BAL-02', 'capacidad' => 2, 'ubicacion' => 'balcon', 'estado' => 'disponible', 'activa' => true],
            
            // Jardín
            ['numero' => 'JAR-01', 'capacidad' => 4, 'ubicacion' => 'jardin', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'JAR-02', 'capacidad' => 6, 'ubicacion' => 'jardin', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'JAR-03', 'capacidad' => 10, 'ubicacion' => 'jardin', 'estado' => 'disponible', 'activa' => true],
            
            // Ventana
            ['numero' => 'VEN-01', 'capacidad' => 2, 'ubicacion' => 'ventana', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'VEN-02', 'capacidad' => 4, 'ubicacion' => 'ventana', 'estado' => 'disponible', 'activa' => true],
            ['numero' => 'VEN-03', 'capacidad' => 4, 'ubicacion' => 'ventana', 'estado' => 'fuera_de_servicio', 'activa' => true],
        ];

        foreach ($mesas as $mesa) {
            Mesa::updateOrCreate(['numero' => $mesa['numero']], $mesa);
        }
    }
}
