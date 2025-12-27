<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cobro extends BaseModel
{
    protected $fillable = [
        'empresa_id', 'numero', 'fecha', 'cliente_id', 'usuario_id',
        'tipo_pago', 'referencia', 'banco', 'comprobante_path',
        'monto', 'estado', 'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
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