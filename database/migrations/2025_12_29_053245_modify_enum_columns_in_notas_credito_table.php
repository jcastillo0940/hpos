<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar columnas ENUM a VARCHAR para mayor flexibilidad
        DB::statement('ALTER TABLE notas_credito MODIFY tipo VARCHAR(50)');
        DB::statement('ALTER TABLE notas_credito MODIFY motivo VARCHAR(255)');
        DB::statement('ALTER TABLE notas_credito MODIFY estado VARCHAR(50)');
    }

    public function down(): void
    {
        // Revertir a ENUM (valores originales)
        DB::statement("ALTER TABLE notas_credito MODIFY tipo ENUM('devolucion','descuento') DEFAULT 'devolucion'");
        DB::statement("ALTER TABLE notas_credito MODIFY motivo ENUM('producto_dañado','producto_vencido','error_facturacion','devolucion_comercial','otro') DEFAULT 'otro'");
        DB::statement("ALTER TABLE notas_credito MODIFY estado ENUM('pendiente','aplicada','anulada') DEFAULT 'pendiente'");
    }
};