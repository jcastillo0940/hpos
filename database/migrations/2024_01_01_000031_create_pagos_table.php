<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_id')->constrained('pagos')->onDelete('cascade');
            $table->foreignId('factura_compra_id')->constrained('facturas_compra');
            
            $table->decimal('monto_aplicado', 15, 2);
            
            $table->timestamps();
            
            $table->index('pago_id');
            $table->index('factura_compra_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos_detalle');
    }
};