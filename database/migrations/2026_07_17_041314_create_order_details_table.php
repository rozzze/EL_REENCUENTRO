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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            // Relación con el pedido
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            // Relación con el producto del inventario
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            // Congelamos el precio en el momento de la venta por si a futuro cambia en el inventario
            $table->decimal('unit_price', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
