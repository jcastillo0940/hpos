<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas_diarias_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_diaria_id')->constrained('rutas_diarias')->onDelete('cascade');
            $table->foreignId('factura_id')->constrained('facturas');
            $table->foreignId('cliente_id')->constrained('clientes');
            
            $table->integer('orden')->default(0)->comment('Orden de visita');
            
            $table->enum('estado', ['pendiente', 'entregada', 'rechazada', 'parcial'])->default('pendiente');
            
            $table->timestamp('fecha_entrega')->nullable();
            $table->string('firma_path')->nullable()->comment('Firma de recibido');
            $table->string('foto_evidencia')->nullable();
            
            $table->enum('forma_pago', ['efectivo', 'cheque', 'transferencia', 'ach', 'credito'])->nullable();
            $table->decimal('monto_cobrado', 15, 2)->default(0);
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            
            $table->index('ruta_diaria_id');
            $table->index('factura_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas_diarias_detalle');
    }
};