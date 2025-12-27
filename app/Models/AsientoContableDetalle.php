<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsientoContableDetalle extends Model
{
    protected $table = 'asientos_contables_detalle';

    protected $fillable = [
        'asiento_contable_id', 'cuenta_id', 'tercero_tipo', 'tercero_id',
        'descripcion', 'debito', 'credito'
    ];

    protected $casts = [
        'debito' => 'decimal:2',
        'credito' => 'decimal:2',
    ];

    public function asientoContable(): BelongsTo
    {
        return $this->belongsTo(AsientoContable::class);
    }

    public function cuenta(): BelongsTo
    {
        return $this->belongsTo(PlanCuenta::class, 'cuenta_id');
    }

    public function tercero()
    {
        return $this->morphTo('tercero', 'tercero_tipo', 'tercero_id');
    }
}