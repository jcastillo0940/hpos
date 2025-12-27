<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepciones_compra_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recepcion_compra_id')->constrained('recepciones_compra')->onDelete('cascade');
            $table->foreignId('orden_compra_detalle_id')->constrained('ordenes_compra_detalle');
            $table->foreignId('producto_id')->constrained('productos');
            
            $table->string('lote')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            
            $table->decimal('cantidad_recibida', 15, 2);
            $table->decimal('precio_unitario', 15, 4);
            
            $table->timestamps();
            
            $table->index('recepcion_compra_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepciones_compra_detalle');
    }
};