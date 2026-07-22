<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Validamos que no exista previamente para evitar errores de duplicado
        if (!User::where('email', 'admin@elencuentro.com')->exists()) {
            User::create([
                'name' => 'Administrador General',
                'email' => 'admin@elencuentro.com',
                'password' => Hash::make('Admin12345!'), // Contraseña segura
                'role' => 'admin',
                'email_verified_at' => now(), // Lo marcamos como verificado
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si hacemos un rollback, eliminamos a este usuario
        User::where('email', 'admin@elencuentro.com')->delete();
    }
};
