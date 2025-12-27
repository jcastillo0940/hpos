<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asientos_contables_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asiento_contable_id')->constrained('asientos_contables')->onDelete('cascade');
            $table->foreignId('cuenta_id')->constrained('plan_cuentas');
            
            $table->string('tercero_tipo')->nullable()->comment('cliente, proveedor, empleado');
            $table->unsignedBigInteger('tercero_id')->nullable();
            
            $table->text('descripcion')->nullable();
            
            $table->decimal('debito', 15, 2)->default(0);
            $table->decimal('credito', 15, 2)->default(0);
            
            $table->timestamps();
            
            $table->index('asiento_contable_id');
            $table->index('cuenta_id');
            $table->index(['tercero_tipo', 'tercero_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asientos_contables_detalle');
    }
};