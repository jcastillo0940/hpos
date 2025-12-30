<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar el tipo de columna de ENUM a VARCHAR
        DB::statement('ALTER TABLE notas_credito MODIFY motivo VARCHAR(255)');
    }

    public function down(): void
    {
        // Revertir a ENUM si es necesario
        DB::statement("ALTER TABLE notas_credito MODIFY motivo ENUM('producto_dañado','producto_vencido','error_facturacion','devolucion_comercial','otro')");
    }
};