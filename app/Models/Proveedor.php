<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proveedor extends BaseModel
{
    protected $table = 'proveedores';

    protected $fillable = [
        'empresa_id', 'codigo', 'ruc', 'dv', 'razon_social',
        'nombre_comercial', 'email', 'telefono', 'direccion',
        'dias_credito', 'saldo_actual', 'contacto_nombre',
        'contacto_telefono', 'observaciones', 'activo'
    ];

    protected $casts = [
        'saldo_actual' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }
}