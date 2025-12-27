<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_entrega_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_entrega_id')->constrained('ordenes_entrega')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos');
            
            $table->decimal('cantidad', 15, 2);
            $table->decimal('precio_unitario', 15, 4);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_monto', 15, 2)->default(0);
            $table->decimal('itbms_porcentaje', 5, 2);
            $table->decimal('itbms_monto', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('total', 15, 2);
            
            $table->timestamps();
            
            $table->index('orden_entrega_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_entrega_detalle');
    }
};