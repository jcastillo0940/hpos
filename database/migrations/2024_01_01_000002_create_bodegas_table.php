<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bodegas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('codigo', 20)->unique();
            $table->string('nombre');
            $table->enum('tipo', ['central', 'movil', 'tienda', 'transito'])->default('central');
            
            // Para bodegas móviles (camiones)
            $table->string('placa_vehiculo', 20)->nullable();
            $table->foreignId('repartidor_id')->nullable()->constrained('users');
            
            $table->text('direccion')->nullable();
            $table->string('responsable')->nullable();
            $table->string('telefono', 20)->nullable();
            
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bodegas');
    }
};