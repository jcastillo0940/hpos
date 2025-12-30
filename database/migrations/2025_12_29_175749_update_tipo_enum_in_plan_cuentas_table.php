<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cambiar ENUM a VARCHAR para mayor flexibilidad
        DB::statement("ALTER TABLE plan_cuentas MODIFY COLUMN tipo VARCHAR(50) NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE plan_cuentas MODIFY COLUMN tipo ENUM('activo','pasivo','patrimonio','ingreso','costo','gasto') NOT NULL");
    }
};