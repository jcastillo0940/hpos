<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrdenEntrega extends BaseModel
{
    protected $table = 'ordenes_entrega';

    protected $fillable = [
        'empresa_id', 'numero', 'fecha', 'cliente_id', 'vendedor_id',
        'zona_id', 'ruta_id', 'subtotal', 'descuento', 'itbms', 'total',
        'estado', 'observaciones', 'firma_cliente', 'factura_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'itbms' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class);
    }

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(OrdenEntregaDetalle::class);
    }

    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class);
    }
}