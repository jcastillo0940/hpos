<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cobros_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cobro_id')->constrained('cobros')->onDelete('cascade');
            $table->foreignId('factura_id')->constrained('facturas');
            
            $table->decimal('monto_aplicado', 15, 2);
            
            $table->timestamps();
            
            $table->index('cobro_id');
            $table->index('factura_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobros_detalle');
    }
};