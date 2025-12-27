<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_entrega', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('vendedor_id')->constrained('users');
            $table->foreignId('zona_id')->nullable()->constrained('zonas');
            $table->foreignId('ruta_id')->nullable()->constrained('rutas');
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('descuento', 15, 2)->default(0);
            $table->decimal('itbms', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            
            $table->enum('estado', ['pendiente', 'aprobada', 'facturada', 'anulada'])->default('pendiente');
            
            $table->text('observaciones')->nullable();
            $table->string('firma_cliente')->nullable()->comment('Path imagen firma');
            
            $table->foreignId('factura_id')->nullable()->constrained('facturas');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'estado', 'fecha']);
            $table->index(['cliente_id', 'vendedor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_entrega');
    }
};