<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacturaCompra extends BaseModel
{
    protected $table = 'facturas_compra';

    protected $fillable = [
        'empresa_id', 'numero_factura', 'fecha', 'fecha_vencimiento',
        'proveedor_id', 'orden_compra_id', 'subtotal', 'itbms', 'total',
        'saldo_pendiente', 'estado', 'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'itbms' => 'decimal:2',
        'total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(FacturaCompraDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoDetalle::class);
    }
}