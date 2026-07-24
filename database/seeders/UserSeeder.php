<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'username' => 'admin',
            'nombre' => 'Administrador',
            'apellido' => 'Sistema',
            'email' => 'admin@cafeaurora.com',
            'password' => Hash::make('admin123'),
            'activo' => true,
        ]);

        DB::table('role_user')->insert([
            'user_id' => $admin->id,
            'role_id' => 1, // Administrador
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $cliente = User::create([
            'username' => 'cliente',
            'nombre' => 'Cliente',
            'apellido' => 'Demo',
            'email' => 'cliente@cafeaurora.com',
            'password' => Hash::make('1234'),
            'activo' => true,
        ]);

        DB::table('role_user')->insert([
            'user_id' => $cliente->id,
            'role_id' => 2, // Cliente
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
