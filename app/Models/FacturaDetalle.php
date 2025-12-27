<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaDetalle extends Model
{
    protected $table = 'facturas_detalle';

    protected $fillable = [
        'factura_id', 'producto_id', 'bodega_id', 'lote', 'fecha_vencimiento',
        'cantidad', 'precio_unitario', 'costo_unitario', 'descuento_porcentaje',
        'descuento_monto', 'itbms_porcentaje', 'itbms_monto', 'subtotal', 'total'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:4',
        'costo_unitario' => 'decimal:4',
        'descuento_porcentaje' => 'decimal:2',
        'descuento_monto' => 'decimal:2',
        'itbms_porcentaje' => 'decimal:2',
        'itbms_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class);
    }
}