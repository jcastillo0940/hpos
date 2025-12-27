<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecepcionCompra extends BaseModel
{
    protected $table = 'recepciones_compra';

    protected $fillable = [
        'empresa_id', 'numero', 'fecha', 'orden_compra_id',
        'proveedor_id', 'bodega_id', 'usuario_id', 'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function ordenCompra(): BelongsTo
    {
        return $this->belongsTo(OrdenCompra::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function bodega(): BelongsTo
    {
        return $this->belongsTo(Bodega::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(RecepcionCompraDetalle::class);
    }
}