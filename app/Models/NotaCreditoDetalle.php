<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotaCreditoDetalle extends Model
{
    protected $table = 'notas_credito_detalle';

    protected $fillable = [
        'nota_credito_id', 'factura_detalle_id', 'producto_id', 'bodega_id',
        'lote', 'fecha_vencimiento', 'cantidad', 'precio_unitario',
        'costo_unitario', 'itbms_porcentaje', 'itbms_monto', 'subtotal', 'total'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:4',
        'costo_unitario' => 'decimal:4',
        'itbms_porcentaje' => 'decimal:2',
        'itbms_monto' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function notaCredito(): BelongsTo
    {
        return $this->belongsTo(NotaCredito::class);
    }

    public function facturaDetalle(): BelongsTo
    {
        return $this->belongsTo(FacturaDetalle::class);
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