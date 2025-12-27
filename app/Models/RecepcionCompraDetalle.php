<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecepcionCompraDetalle extends Model
{
    protected $table = 'recepciones_compra_detalle';

    protected $fillable = [
        'recepcion_compra_id', 'orden_compra_detalle_id', 'producto_id',
        'lote', 'fecha_vencimiento', 'cantidad_recibida', 'precio_unitario'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad_recibida' => 'decimal:2',
        'precio_unitario' => 'decimal:4',
    ];

    public function recepcionCompra(): BelongsTo
    {
        return $this->belongsTo(RecepcionCompra::class);
    }

    public function ordenCompraDetalle(): BelongsTo
    {
        return $this->belongsTo(OrdenCompraDetalle::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}