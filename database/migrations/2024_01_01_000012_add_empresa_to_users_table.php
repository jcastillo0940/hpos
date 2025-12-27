<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('empresa_id')->nullable()->after('id')->constrained('empresas');
            $table->string('codigo_empleado', 20)->nullable()->after('empresa_id');
            $table->string('telefono', 20)->nullable()->after('email');
            $table->boolean('activo')->default(true)->after('password');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['empresa_id']);
            $table->dropColumn(['empresa_id', 'codigo_empleado', 'telefono', 'activo', 'deleted_at']);
        });
    }
};