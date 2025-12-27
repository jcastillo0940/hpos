<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('usuario_id')->constrained('users')->comment('Quien registró el cobro');
            
            $table->enum('tipo_pago', ['efectivo', 'cheque', 'transferencia', 'ach'])->default('efectivo');
            $table->string('referencia')->nullable()->comment('Número cheque/transferencia');
            $table->string('banco')->nullable();
            $table->string('comprobante_path')->nullable()->comment('Foto del comprobante');
            
            $table->decimal('monto', 15, 2);
            
            $table->enum('estado', ['pendiente', 'aplicado', 'anulado'])->default('pendiente');
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'fecha', 'estado']);
            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobros');
    }
};