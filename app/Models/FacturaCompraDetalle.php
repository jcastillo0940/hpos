<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacturaCompraDetalle extends Model
{
    protected $table = 'facturas_compra_detalle';

    protected $fillable = [
        'factura_compra_id', 'producto_id', 'cantidad', 'precio_unitario',
        'itbms_porcentaje', 'itbms_monto', 'subtotal', 'total'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:4',
        'itbms_porcentaje' => 'decimal:2',
        'itbms_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function facturaCompra(): BelongsTo
    {
        return $this->belongsTo(FacturaCompra::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}