<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listas_precios_productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lista_precio_id')->constrained('listas_precios')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            
            // Tipo: fijo o porcentaje
            $table->enum('tipo_precio', ['fijo', 'porcentaje'])->default('fijo');
            
            // Precio fijo
            $table->decimal('precio', 12, 2)->nullable();
            
            // Porcentaje sobre precio base (+10%, -5%, etc)
            $table->decimal('porcentaje', 8, 2)->nullable()->comment('Porcentaje de aumento o descuento');
            
            // Precio calculado (se actualiza automáticamente)
            $table->decimal('precio_calculado', 12, 2)->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->unique(['lista_precio_id', 'producto_id']);
            $table->index('lista_precio_id');
            $table->index('producto_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listas_precios_productos');
    }
};