<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            
            $table->foreignId('proveedor_id')->constrained('proveedores');
            $table->foreignId('usuario_id')->constrained('users');
            
            $table->enum('tipo_pago', ['efectivo', 'cheque', 'transferencia', 'ach'])->default('transferencia');
            $table->string('referencia')->nullable();
            $table->string('banco')->nullable();
            
            $table->decimal('monto', 15, 2);
            
            $table->enum('estado', ['pendiente', 'aplicado', 'anulado'])->default('pendiente');
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'fecha', 'estado']);
            $table->index('proveedor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};