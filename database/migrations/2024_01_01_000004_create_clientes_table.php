<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            
            // Identificación
            $table->string('codigo', 20)->comment('Código interno');
            $table->enum('tipo_identificacion', ['ruc', 'cedula', 'pasaporte'])->default('ruc');
            $table->string('identificacion', 30);
            $table->string('dv', 2)->nullable();
            
            // Datos generales
            $table->enum('tipo_cliente', ['juridico', 'natural'])->default('juridico');
            $table->string('razon_social')->nullable();
            $table->string('nombre_comercial');
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            
            // Ubicación
            $table->text('direccion')->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('corregimiento', 100)->nullable();
            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();
            
            // Zona de venta
            $table->foreignId('zona_id')->nullable()->constrained('zonas');
            $table->foreignId('ruta_id')->nullable()->constrained('rutas');
            $table->foreignId('vendedor_id')->nullable()->constrained('users');
            
            // Crédito
            $table->decimal('limite_credito', 15, 2)->default(0);
            $table->integer('dias_credito')->default(0);
            $table->decimal('saldo_actual', 15, 2)->default(0)->comment('CxC actual');
            $table->decimal('saldo_vencido', 15, 2)->default(0);
            
            // Precios y descuentos
            $table->foreignId('lista_precio_id')->nullable()->constrained('listas_precios');
            $table->decimal('descuento_general', 5, 2)->default(0)->comment('% descuento');
            
            // Contacto principal
            $table->string('contacto_nombre')->nullable();
            $table->string('contacto_cargo')->nullable();
            $table->string('contacto_telefono', 20)->nullable();
            $table->string('contacto_email')->nullable();
            
            // Campos dinámicos (JSON)
            $table->json('campos_extra')->nullable()->comment('Campos personalizados');
            
            // Observaciones
            $table->text('observaciones')->nullable();
            
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['empresa_id', 'codigo']);
            $table->index(['empresa_id', 'vendedor_id']);
            $table->index(['empresa_id', 'zona_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};