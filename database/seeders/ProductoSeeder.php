<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catBebidasCalientes = DB::table('categorias')->where('nombre', 'Bebidas Calientes')->value('id');
        $catBebidasFrias = DB::table('categorias')->where('nombre', 'Bebidas Frías')->value('id');
        $catPostres = DB::table('categorias')->where('nombre', 'Postres')->value('id');
        $catSnacks = DB::table('categorias')->where('nombre', 'Snacks')->value('id');

        $productos = [
            // Bebidas Calientes
            [
                'categoria_id' => $catBebidasCalientes,
                'codigo' => '1001',
                'nombre' => 'Café Americano',
                'descripcion' => 'Café filtrado suave y aromático',
                'precio' => 38.00,
                'existencia' => 40,
                'imagen' => 'images/productos/americano.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catBebidasCalientes,
                'codigo' => '1002',
                'nombre' => 'Espresso',
                'descripcion' => 'Café concentrado intenso',
                'precio' => 32.00,
                'existencia' => 35,
                'imagen' => 'images/productos/espresso.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catBebidasCalientes,
                'codigo' => '1003',
                'nombre' => 'Capuccino',
                'descripcion' => 'Espresso con leche espumada y canela',
                'precio' => 52.00,
                'existencia' => 28,
                'imagen' => 'images/productos/capuccino.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catBebidasCalientes,
                'codigo' => '1004',
                'nombre' => 'Latte',
                'descripcion' => 'Espresso con abundante leche cremosa',
                'precio' => 50.00,
                'existencia' => 30,
                'imagen' => 'images/productos/latte.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catBebidasCalientes,
                'codigo' => '1005',
                'nombre' => 'Moka',
                'descripcion' => 'Espresso con chocolate y leche',
                'precio' => 58.00,
                'existencia' => 26,
                'imagen' => 'images/productos/moka.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catBebidasCalientes,
                'codigo' => '1006',
                'nombre' => 'Chocolate Caliente',
                'descripcion' => 'Chocolate belga con leche espumada',
                'precio' => 48.00,
                'existencia' => 24,
                'imagen' => 'images/productos/chocolate-caliente.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catBebidasCalientes,
                'codigo' => '1007',
                'nombre' => 'Té Verde',
                'descripcion' => 'Té verde orgánico importado',
                'precio' => 34.00,
                'existencia' => 32,
                'imagen' => 'images/productos/te-verde.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Bebidas Frías
            [
                'categoria_id' => $catBebidasFrias,
                'codigo' => '1008',
                'nombre' => 'Frappé Moka',
                'descripcion' => 'Frappé de café con chocolate y crema',
                'precio' => 68.00,
                'existencia' => 22,
                'imagen' => 'images/productos/frappe-moka.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catBebidasFrias,
                'codigo' => '1009',
                'nombre' => 'Frappé Vainilla',
                'descripcion' => 'Frappé cremoso de vainilla',
                'precio' => 66.00,
                'existencia' => 22,
                'imagen' => 'images/productos/frappe-vainilla.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catBebidasFrias,
                'codigo' => '1010',
                'nombre' => 'Frappé Caramelo',
                'descripcion' => 'Frappé con salsa de caramelo',
                'precio' => 69.00,
                'existencia' => 20,
                'imagen' => 'images/productos/frappe-caramelo.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Postres
            [
                'categoria_id' => $catPostres,
                'codigo' => '1011',
                'nombre' => 'Dona Glaseada',
                'descripcion' => 'Dona artesanal con glaseado clásico',
                'precio' => 28.00,
                'existencia' => 45,
                'imagen' => 'images/productos/dona-glaseada.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catPostres,
                'codigo' => '1012',
                'nombre' => 'Brownie',
                'descripcion' => 'Brownie de chocolate con nueces',
                'precio' => 42.00,
                'existencia' => 25,
                'imagen' => 'images/productos/brownie.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catPostres,
                'codigo' => '1013',
                'nombre' => 'Cheesecake',
                'descripcion' => 'Cheesecake de queso crema con frutos rojos',
                'precio' => 62.00,
                'existencia' => 18,
                'imagen' => 'images/productos/cheesecake.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'categoria_id' => $catPostres,
                'codigo' => '1014',
                'nombre' => 'Muffin Chocolate',
                'descripcion' => 'Muffin de chocolate con chips',
                'precio' => 36.00,
                'existencia' => 30,
                'imagen' => 'images/productos/muffin-chocolate.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Snacks
            [
                'categoria_id' => $catSnacks,
                'codigo' => '1015',
                'nombre' => 'Sándwich Club',
                'descripcion' => 'Sándwich triple con jamón, queso y verduras',
                'precio' => 78.00,
                'existencia' => 16,
                'imagen' => 'images/productos/sandwich-club.png',
                'disponible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('productos')->insert($productos);
    }
}
