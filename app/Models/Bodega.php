<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bodega extends BaseModel
{
    protected $fillable = [
        'empresa_id', 'codigo', 'nombre', 'tipo', 'placa_vehiculo',
        'repartidor_id', 'direccion', 'responsable', 'telefono', 'activa'
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function repartidor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'repartidor_id');
    }
}