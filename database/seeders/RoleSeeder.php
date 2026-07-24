<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'id' => 1,
                'name' => 'Administrador',
                'description' => 'Acceso total al sistema',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Cliente',
                'description' => 'Cliente de la cafetería',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
