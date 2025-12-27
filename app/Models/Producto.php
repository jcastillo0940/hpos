<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends BaseModel
{
    protected $fillable = [
        'empresa_id', 'categoria_id', 'codigo', 'codigo_barra', 'nombre',
        'descripcion', 'tipo', 'unidad_medida', 'imagen', 'costo_unitario',
        'precio_venta', 'precio_mayorista', 'maneja_inventario', 'maneja_lote',
        'maneja_vencimiento', 'stock_minimo', 'stock_maximo', 'itbms',
        'campos_extra', 'activo'
    ];

    protected $casts = [
        'costo_unitario' => 'decimal:4',
        'precio_venta' => 'decimal:2',
        'precio_mayorista' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
        'stock_maximo' => 'decimal:2',
        'itbms' => 'decimal:2',
        'maneja_inventario' => 'boolean',
        'maneja_lote' => 'boolean',
        'maneja_vencimiento' => 'boolean',
        'activo' => 'boolean',
        'campos_extra' => 'array',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockTotal()
    {
        return $this->stocks()->sum('cantidad_disponible');
    }
}