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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            // Relación: ¿A qué proveedor le compramos?
            $table->foreignId('supplier_id')->constrained()->onDelete('restrict');
            $table->decimal('total', 10, 2)->default(0);
            $table->string('status')->default('completado'); // o "pendiente" si llega después
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
