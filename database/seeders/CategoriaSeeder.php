<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categorias')->insert([
            [
                'nombre' => 'Bebidas Calientes',
                'descripcion' => 'Café, té y bebidas calientes',
                'icono' => 'bi-cup-hot-fill',
                'orden' => 1,
                'activa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Bebidas Frías',
                'descripcion' => 'Frappes y bebidas frías',
                'icono' => 'bi-cup-straw',
                'orden' => 2,
                'activa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Postres',
                'descripcion' => 'Pasteles, donas y dulces',
                'icono' => 'bi-cake2-fill',
                'orden' => 3,
                'activa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Snacks',
                'descripcion' => 'Sándwiches y bocadillos',
                'icono' => 'bi-egg-fried',
                'orden' => 4,
                'activa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
