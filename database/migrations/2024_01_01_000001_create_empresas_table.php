<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 20)->unique()->comment('RUC de la empresa');
            $table->string('dv', 2)->nullable()->comment('Dígito verificador');
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->text('direccion')->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('corregimiento', 100)->nullable();
            $table->string('logo')->nullable();
            
            // Configuración contable
            $table->boolean('usa_multibodega')->default(true);
            $table->boolean('usa_lotes')->default(false);
            $table->boolean('usa_vencimientos')->default(true);
            $table->string('metodo_costeo')->default('promedio'); // promedio, fifo, ultimo
            
            // Facturación electrónica
            $table->boolean('facturacion_electronica')->default(true);
            $table->string('pac_proveedor')->nullable(); // Proveedor autorizado certificación
            $table->text('pac_config')->nullable(); // JSON con credenciales
            
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};