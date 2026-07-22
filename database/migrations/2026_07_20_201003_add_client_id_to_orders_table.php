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
            Schema::table('orders', function (Blueprint $table) {
                // Agregamos la relación, permitiendo que sea nulo para las "ventas rápidas"
                $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->nullOnDelete();
            });
        }

    public function down(): void
        {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['client_id']);
                $table->dropColumn('client_id');
            });
        }
};
