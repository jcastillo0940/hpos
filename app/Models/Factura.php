<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Factura extends BaseModel
{
    protected $fillable = [
        'empresa_id',
        'numero',
        'fecha',
        'fecha_vencimiento',
        'cliente_id',
        'cliente_sucursal_id',
        'vendedor_id',
        'orden_entrega_id',
        'subtotal',
        'descuento',
        'itbms',
        'total',
        'tipo_pago',
        'saldo_pendiente',
        'estado',
        'cufe',
        'xml_path',
        'pdf_path',
        'observaciones',
        'anulada_motivo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'itbms' => 'decimal:2',
        'total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteSucursal(): BelongsTo
    {
        return $this->belongsTo(ClienteSucursal::class);
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function ordenEntrega(): BelongsTo
    {
        return $this->belongsTo(OrdenEntrega::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(FacturaDetalle::class);
    }

    public function cobros(): HasMany
    {
        return $this->hasMany(CobroDetalle::class);
    }

    public function notasCredito(): HasMany
    {
        return $this->hasMany(NotaCredito::class);
    }
}