<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    protected $fillable = [
        'empresa_id', 'bodega_id', 'producto_id', 'lote', 'fecha_vencimiento',
        'cantidad', 'cantidad_reservada', 'cantidad_disponible', 'costo_unitario'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'cantidad_reservada' => 'decimal:2',
        'cantidad_disponible' => 'decimal:2',
        'costo_unitario' => 'decimal:4',
        'fecha_vencimiento' => 'date',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}