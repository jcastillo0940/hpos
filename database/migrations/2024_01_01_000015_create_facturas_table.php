<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            $table->date('fecha_vencimiento')->nullable();
            
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->foreignId('vendedor_id')->constrained('users');
            
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('descuento', 15, 2)->default(0);
            $table->decimal('itbms', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            
            $table->enum('tipo_pago', ['contado', 'credito'])->default('credito');
            $table->decimal('saldo_pendiente', 15, 2)->default(0);
            
            $table->enum('estado', ['pendiente', 'pagada', 'parcial', 'vencida', 'anulada'])->default('pendiente');
            
            $table->string('cufe')->nullable()->comment('Código Único Factura Electrónica');
            $table->string('xml_path')->nullable();
            $table->string('pdf_path')->nullable();
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'estado', 'fecha']);
            $table->index(['cliente_id', 'vendedor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};