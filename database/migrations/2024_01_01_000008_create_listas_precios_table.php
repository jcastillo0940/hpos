<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listas_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('codigo', 20);
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->boolean('es_default')->default(false);
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['empresa_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listas_precios');
    }
};