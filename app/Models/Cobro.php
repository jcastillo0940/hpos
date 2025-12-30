<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cobro extends BaseModel
{
    protected $fillable = [
    'empresa_id', 'numero', 'fecha', 'cliente_id', 'usuario_id',
    'tipo_pago', 'es_factoring', 'descuento_factoring', 'porcentaje_factoring', 'financiera',
    'referencia', 'banco', 'comprobante_path',
    'monto', 'estado', 'observaciones'
];

protected $casts = [
    'fecha' => 'date',
    'monto' => 'decimal:2',
    'descuento_factoring' => 'decimal:2',
    'porcentaje_factoring' => 'decimal:2',
    'es_factoring' => 'boolean',
];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CobroDetalle::class);
    }
}