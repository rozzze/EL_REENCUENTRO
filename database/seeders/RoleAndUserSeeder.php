<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear los roles del restaurante
        $adminRole = Role::create(['name' => 'admin']);
        $empleadoRole = Role::create(['name' => 'empleado']);
        $clienteRole = Role::create(['name' => 'cliente']);

        // 2. Crear tu usuario Administrador
        $admin = User::create([
            'name' => 'Administrador El Encuentro',
            'email' => 'admin@elencuentro.com',
            'password' => bcrypt('password123'), // Puedes cambiarla luego
            'phone' => '999888777',
        ]);

        // 3. Asignarle el rol de poder absoluto
        $admin->assignRole($adminRole);
    }
}
