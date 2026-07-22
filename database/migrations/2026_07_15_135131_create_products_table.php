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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // Ej: Lácteos, Carnes, Bebidas[cite: 1]
            $table->decimal('price', 8, 2)->default(0); // Precio para asignación a platos[cite: 1]
            $table->integer('stock')->default(0); // Control de disponibilidad[cite: 1]
            $table->integer('min_stock')->default(10); // Límite para notificaciones de escasez[cite: 1]
            $table->date('expiration_date')->nullable(); // Control de vencimiento[cite: 1]
            $table->string('status')->default('active'); // Para registrar productos malogrados/vencidos[cite: 1]
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
