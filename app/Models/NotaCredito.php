<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotaCredito extends BaseModel
{
    protected $table = 'notas_credito';

    protected $fillable = [
        'empresa_id', 'numero', 'fecha', 'factura_id', 'cliente_id',
        'tipo', 'motivo', 'subtotal', 'itbms', 'total', 'estado',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'itbms' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(NotaCreditoDetalle::class);
    }
}