<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias');
            
            $table->string('codigo', 50)->comment('SKU');
            $table->string('codigo_barra', 50)->nullable()->comment('EAN/UPC');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            
            $table->enum('tipo', ['producto', 'servicio', 'combo'])->default('producto');
            $table->string('unidad_medida', 20)->default('UND');
            
            $table->string('imagen')->nullable();
            
            $table->decimal('costo_unitario', 15, 4)->default(0);
            $table->decimal('precio_venta', 15, 2)->default(0);
            $table->decimal('precio_mayorista', 15, 2)->nullable();
            
            $table->boolean('maneja_inventario')->default(true);
            $table->boolean('maneja_lote')->default(false);
            $table->boolean('maneja_vencimiento')->default(false);
            
            $table->decimal('stock_minimo', 15, 2)->default(0);
            $table->decimal('stock_maximo', 15, 2)->default(0);
            
            $table->decimal('itbms', 5, 2)->default(7.00)->comment('% ITBMS');
            
            $table->json('campos_extra')->nullable();
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['empresa_id', 'codigo']);
            $table->index(['empresa_id', 'categoria_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};