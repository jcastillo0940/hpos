<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cobros', function (Blueprint $table) {
            $table->boolean('es_factoring')->default(false)->after('tipo_pago');
            $table->decimal('descuento_factoring', 12, 2)->default(0)->after('es_factoring');
            $table->decimal('porcentaje_factoring', 5, 2)->default(0)->after('descuento_factoring');
            $table->string('financiera')->nullable()->after('porcentaje_factoring');
        });
    }

    public function down(): void
    {
        Schema::table('cobros', function (Blueprint $table) {
            $table->dropColumn(['es_factoring', 'descuento_factoring', 'porcentaje_factoring', 'financiera']);
        });
    }
};