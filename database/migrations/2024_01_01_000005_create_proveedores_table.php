<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            
            $table->string('codigo', 20);
            $table->string('ruc', 30);
            $table->string('dv', 2)->nullable();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            
            // Crédito
            $table->integer('dias_credito')->default(0);
            $table->decimal('saldo_actual', 15, 2)->default(0)->comment('CxP actual');
            
            // Contacto
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['empresa_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};