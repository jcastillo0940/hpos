<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsientoContable extends BaseModel
{
    protected $table = 'asientos_contables';

    protected $fillable = [
        'empresa_id', 'numero', 'fecha', 'tipo', 'origen', 'origen_id',
        'concepto', 'total_debito', 'total_credito', 'estado', 'usuario_id'
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_debito' => 'decimal:2',
        'total_credito' => 'decimal:2',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(AsientoContableDetalle::class);
    }
}