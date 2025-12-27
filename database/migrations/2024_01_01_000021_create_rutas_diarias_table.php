<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rutas_diarias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            
            $table->foreignId('repartidor_id')->constrained('users');
            $table->foreignId('bodega_id')->nullable()->constrained('bodegas')->comment('Bodega móvil/camión');
            $table->foreignId('ruta_id')->nullable()->constrained('rutas');
            
            $table->enum('estado', ['pendiente', 'en_proceso', 'completada', 'liquidada'])->default('pendiente');
            
            $table->decimal('total_efectivo', 15, 2)->default(0);
            $table->decimal('total_cheques', 15, 2)->default(0);
            $table->decimal('total_transferencias', 15, 2)->default(0);
            $table->decimal('total_credito', 15, 2)->default(0);
            $table->decimal('total_ruta', 15, 2)->default(0);
            
            $table->decimal('efectivo_entregado', 15, 2)->default(0);
            $table->decimal('diferencia', 15, 2)->default(0)->comment('Faltante/Sobrante');
            
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_fin')->nullable();
            $table->timestamp('fecha_liquidacion')->nullable();
            $table->foreignId('liquidado_por')->nullable()->constrained('users');
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'fecha', 'estado']);
            $table->index('repartidor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas_diarias');
    }
};