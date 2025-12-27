<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ordenes_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            $table->date('fecha_entrega_esperada')->nullable();
            
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('bodega_destino_id')->constrained('bodegas');
            $table->foreignId('usuario_id')->constrained('users')->comment('Quien creó la OC');
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('itbms', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            
            $table->enum('estado', ['borrador', 'aprobada', 'recibida', 'facturada', 'anulada'])->default('borrador');
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'estado', 'fecha']);
            $table->index('proveedor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ordenes_compra');
    }
};