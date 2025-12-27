<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas_credito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            
            $table->foreignId('factura_id')->constrained('facturas');
            $table->foreignId('cliente_id')->constrained('clientes');
            
            $table->enum('tipo', ['devolucion', 'merma'])->comment('devolucion: reingresa stock, merma: no reingresa');
            $table->enum('motivo', ['producto_dañado', 'producto_vencido', 'error_facturacion', 'devolucion_comercial', 'otro']);
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('itbms', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            
            $table->enum('estado', ['activa', 'anulada'])->default('activa');
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'fecha']);
            $table->index(['factura_id', 'cliente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas_credito');
    }
};