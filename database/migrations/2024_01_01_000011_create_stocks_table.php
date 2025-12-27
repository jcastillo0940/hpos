<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('bodega_id')->constrained('bodegas');
            $table->foreignId('producto_id')->constrained('productos');
            
            $table->string('lote')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            
            $table->decimal('cantidad', 15, 2)->default(0);
            $table->decimal('cantidad_reservada', 15, 2)->default(0);
            $table->decimal('cantidad_disponible', 15, 2)->default(0);
            
            $table->decimal('costo_unitario', 15, 4)->default(0);
            
            $table->timestamps();
            
            $table->unique(['bodega_id', 'producto_id', 'lote', 'fecha_vencimiento'], 'stock_unique');
            $table->index(['empresa_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};