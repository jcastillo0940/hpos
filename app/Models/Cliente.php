<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'empresa_id',
        'codigo',
        'tipo_identificacion',
        'identificacion',
        'razon_social',
        'nombre_comercial',
        'email',
        'telefono',
        'celular',
        'direccion',
        'provincia',
        'distrito',
        'corregimiento',
        'zona_id',
        'ruta_id',
        'vendedor_id',
        'lista_precio_id',
        'tipo_cliente',
        'limite_credito',
        'dias_credito',
        'descuento_porcentaje',
        'saldo_actual',
        'saldo_vencido',
        'latitud',
        'longitud',
        'dias_visita',
        'orden_visita',
        'campos_adicionales',
        'activo',
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'dias_credito' => 'integer',
        'descuento_porcentaje' => 'decimal:2',
        'saldo_actual' => 'decimal:2',
        'saldo_vencido' => 'decimal:2',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'campos_adicionales' => 'array',
        'activo' => 'boolean',
    ];

    // Relaciones
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function listaPrecio()
    {
        return $this->belongsTo(ListaPrecio::class);
    }

    public function sucursales()
    {
        return $this->hasMany(ClienteSucursal::class);
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    public function ordenesEntrega()
    {
        return $this->hasMany(OrdenEntrega::class);
    }

    public function cobros()
    {
        return $this->hasMany(Cobro::class);
    }
}