<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes_entrega', function (Blueprint $table) {
            $table->foreignId('cliente_sucursal_id')->nullable()->after('cliente_id')->constrained('clientes_sucursales')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes_entrega', function (Blueprint $table) {
            $table->dropForeign(['cliente_sucursal_id']);
            $table->dropColumn('cliente_sucursal_id');
        });
    }
};