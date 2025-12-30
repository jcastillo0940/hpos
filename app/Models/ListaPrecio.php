<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListaPrecio extends BaseModel
{
    use HasFactory;

    protected $table = 'listas_precios';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nombre',
        'descripcion',
        'es_default',
        'activa',
    ];

    protected $casts = [
        'es_default' => 'boolean',
        'activa' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'listas_precios_productos')
            ->withPivot(['tipo_precio', 'precio', 'porcentaje', 'precio_calculado'])
            ->withTimestamps();
    }

    public function productosDetalle(): HasMany
    {
        return $this->hasMany(ListaPrecioProducto::class);
    }

    // Obtener precio de un producto específico
    public function getPrecioProducto($productoId)
    {
        $detalle = $this->productosDetalle()->where('producto_id', $productoId)->first();
        
        if (!$detalle) {
            return null;
        }

        return $detalle->precio_calculado;
    }

    // Recalcular precios de todos los productos
    public function recalcularPrecios()
    {
        foreach ($this->productosDetalle as $detalle) {
            $detalle->calcularPrecio();
            $detalle->save();
        }
    }
}