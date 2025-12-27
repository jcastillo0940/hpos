<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenCompraDetalle extends Model
{
    protected $table = 'ordenes_compra_detalle';

    protected $fillable = [
        'orden_compra_id', 'producto_id', 'cantidad_solicitada',
        'cantidad_recibida', 'precio_unitario', 'itbms_porcentaje',
        'itbms_monto', 'subtotal', 'total'
    ];

    protected $casts = [
        'cantidad_solicitada' => 'decimal:2',
        'cantidad_recibida' => 'decimal:2',
        'precio_unitario' => 'decimal:4',
        'itbms_porcentaje' => 'decimal:2',
        'itbms_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}