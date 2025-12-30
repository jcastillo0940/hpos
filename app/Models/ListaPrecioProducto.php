<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListaPrecioProducto extends Model
{
    protected $table = 'listas_precios_productos';

    protected $fillable = [
        'lista_precio_id',
        'producto_id',
        'tipo_precio',
        'precio',
        'porcentaje',
        'precio_calculado',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'porcentaje' => 'decimal:2',
        'precio_calculado' => 'decimal:2',
    ];

    public function listaPrecio(): BelongsTo
    {
        return $this->belongsTo(ListaPrecio::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    // Calcular precio según tipo
    public function calcularPrecio()
    {
        if ($this->tipo_precio === 'fijo') {
            $this->precio_calculado = $this->precio;
        } else {
            // Porcentaje sobre precio_venta del producto
            $producto = $this->producto;
            $precioBase = $producto->precio_venta;
            
            // Si porcentaje es positivo: aumenta, si es negativo: descuenta
            $this->precio_calculado = $precioBase + ($precioBase * ($this->porcentaje / 100));
        }
    }

    // Observer para calcular automáticamente
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detalle) {
            $detalle->calcularPrecio();
        });
    }
}