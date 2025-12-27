<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asientos_contables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('numero', 20)->unique();
            $table->date('fecha');
            
            $table->enum('tipo', ['manual', 'automatico'])->default('automatico');
            $table->string('origen')->nullable()->comment('factura, cobro, nota_credito, compra, etc');
            $table->unsignedBigInteger('origen_id')->nullable()->comment('ID del documento origen');
            
            $table->text('concepto');
            $table->decimal('total_debito', 15, 2)->default(0);
            $table->decimal('total_credito', 15, 2)->default(0);
            
            $table->enum('estado', ['borrador', 'contabilizado', 'anulado'])->default('contabilizado');
            
            $table->foreignId('usuario_id')->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['empresa_id', 'fecha', 'estado']);
            $table->index(['origen', 'origen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asientos_contables');
    }
};