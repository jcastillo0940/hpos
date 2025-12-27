<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanCuenta extends BaseModel
{
    protected $table = 'plan_cuentas';

    protected $fillable = [
        'empresa_id', 'codigo', 'nombre', 'descripcion', 'tipo',
        'naturaleza', 'nivel', 'cuenta_padre_id', 'acepta_movimiento',
        'requiere_tercero', 'requiere_centro_costo', 'es_sistema',
        'cuenta_sistema', 'activa'
    ];

    protected $casts = [
        'acepta_movimiento' => 'boolean',
        'requiere_tercero' => 'boolean',
        'requiere_centro_costo' => 'boolean',
        'es_sistema' => 'boolean',
        'activa' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cuentaPadre(): BelongsTo
    {
        return $this->belongsTo(PlanCuenta::class, 'cuenta_padre_id');
    }

    public function subcuentas(): HasMany
    {
        return $this->hasMany(PlanCuenta::class, 'cuenta_padre_id');
    }
}