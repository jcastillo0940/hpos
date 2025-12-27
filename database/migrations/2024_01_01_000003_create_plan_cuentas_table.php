<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_cuentas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('codigo', 20)->comment('Ej: 1.1.01.01');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            
            $table->enum('tipo', [
                'activo', 
                'pasivo', 
                'patrimonio', 
                'ingreso', 
                'costo', 
                'gasto'
            ]);
            
            $table->enum('naturaleza', ['debito', 'credito']);
            
            // Control de nivel (para jerarquía)
            $table->integer('nivel')->default(1); // 1, 1.1, 1.1.01, 1.1.01.01
            $table->foreignId('cuenta_padre_id')->nullable()->constrained('plan_cuentas');
            
            // Control de uso
            $table->boolean('acepta_movimiento')->default(true)->comment('False = cuenta de agrupación');
            $table->boolean('requiere_tercero')->default(false)->comment('True = necesita cliente/proveedor');
            $table->boolean('requiere_centro_costo')->default(false);
            
            // Sistema
            $table->boolean('es_sistema')->default(false)->comment('True = no se puede eliminar');
            $table->string('cuenta_sistema')->nullable()->comment('caja, banco, inventario, cxc, cxp, ventas, etc');
            
            $table->boolean('activa')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['empresa_id', 'codigo']);
            $table->index(['empresa_id', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_cuentas');
    }
};