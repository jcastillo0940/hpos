<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepciones_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            
            $table->foreignId('orden_compra_id')->constrained('ordenes_compra');
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('bodega_id')->constrained('bodegas');
            $table->foreignId('usuario_id')->constrained('users')->comment('Quien recibió');
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'fecha']);
            $table->index('orden_compra_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepciones_compra');
    }
};