<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_sucursales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained()->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->string('codigo', 50);
            $table->string('nombre');
            $table->string('contacto')->nullable();
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->text('direccion');
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('corregimiento', 100)->nullable();
            $table->foreignId('zona_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('ruta_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('lista_precio_id')->nullable()->constrained('listas_precios')->onDelete('set null');
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->json('dias_visita')->nullable();
            $table->integer('orden_visita')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['empresa_id', 'cliente_id']);
            $table->index(['zona_id', 'ruta_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_sucursales');
    }
};