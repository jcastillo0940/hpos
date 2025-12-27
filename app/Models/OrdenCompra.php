<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenCompra extends BaseModel
{
    protected $table = 'ordenes_compra';

    protected $fillable = [
        'empresa_id', 'numero', 'fecha', 'fecha_entrega_esperada',
        'proveedor_id', 'bodega_destino_id', 'usuario_id', 'subtotal',
        'itbms', 'total', 'estado', 'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_entrega_esperada' => 'date',
        'subtotal' => 'decimal:2',
        'itbms' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function bodegaDestino(): BelongsTo
    {
        return $this->belongsTo(Bodega::class, 'bodega_destino_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(OrdenCompraDetalle::class);
    }

    public function recepciones(): HasMany
    {
        return $this->hasMany(RecepcionCompra::class);
    }
}