<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenEntregaDetalle extends Model
{
    protected $table = 'ordenes_entrega_detalle';

    protected $fillable = [
        'orden_entrega_id', 'producto_id', 'cantidad', 'precio_unitario',
        'descuento_porcentaje', 'descuento_monto', 'itbms_porcentaje',
        'itbms_monto', 'subtotal', 'total'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:4',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'itbms_porcentaje' => 'decimal:2',
        'itbms_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function ordenEntrega(): BelongsTo
    {
        return $this->belongsTo(OrdenEntrega::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}