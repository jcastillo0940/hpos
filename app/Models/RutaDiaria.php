<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RutaDiaria extends BaseModel
{
    protected $table = 'rutas_diarias';

    protected $fillable = [
        'empresa_id', 'numero', 'fecha', 'repartidor_id', 'bodega_id',
        'ruta_id', 'estado', 'total_efectivo', 'total_cheques',
        'total_transferencias', 'total_credito', 'total_ruta',
        'efectivo_entregado', 'diferencia', 'fecha_inicio', 'fecha_fin',
        'fecha_liquidacion', 'liquidado_por', 'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_efectivo' => 'decimal:2',
        'total_cheques' => 'decimal:2',
        'total_transferencias' => 'decimal:2',
        'total_credito' => 'decimal:2',
        'total_ruta' => 'decimal:2',
        'efectivo_entregado' => 'decimal:2',
        'diferencia' => 'decimal:2',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'fecha_liquidacion' => 'datetime',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class);
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class);
    }

    public function liquidadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'liquidado_por');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(RutaDiariaDetalle::class);
    }
}