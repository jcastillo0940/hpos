<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClienteSucursal extends BaseModel
{
    use HasFactory;

    protected $table = 'clientes_sucursales';

    protected $fillable = [
        'empresa_id',
        'cliente_id',
        'codigo',
        'nombre',
        'contacto',
        'telefono',
        'email',
        'direccion',
        'provincia',
        'distrito',
        'corregimiento',
        'zona_id',
        'ruta_id',
        'lista_precio_id',
        'latitud',
        'longitud',
        'dias_visita',
        'orden_visita',
        'observaciones',
        'activa',
    ];

    protected $casts = [
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
        'dias_visita' => 'array',
        'activa' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    public function listaPrecio()
    {
        return $this->belongsTo(ListaPrecio::class);
    }
}