<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas_compra_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('factura_compra_id')->constrained('facturas_compra')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            
            $table->decimal('cantidad', 15, 2);
            $table->decimal('precio_unitario', 15, 4);
            $table->decimal('itbms_porcentaje', 5, 2)->default(0);
            $table->decimal('itbms_monto', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total', 15, 2);
            
            $table->timestamps();
            
            $table->index('factura_compra_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas_compra_detalle');
    }
};