<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero_factura', 50)->comment('Número de factura del proveedor');
            $table->date('fecha');
            $table->date('fecha_vencimiento')->nullable();
            
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('orden_compra_id')->nullable()->constrained('ordenes_compra');
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('itbms', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('saldo_pendiente', 15, 2)->default(0);
            
            $table->enum('estado', ['pendiente', 'pagada', 'parcial', 'vencida', 'anulada'])->default('pendiente');
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'fecha', 'estado']);
            $table->index('proveedor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas_compra');
    }
};