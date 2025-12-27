<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrdenEntrega extends BaseModel
{
    use HasFactory;

    protected $table = 'ordenes_entrega';

    protected $fillable = [
        'empresa_id',
        'numero',
        'cliente_id',
        'cliente_sucursal_id',
        'vendedor_id',
        'bodega_id',
        'fecha',
        'fecha_entrega',
        'subtotal',
        'itbms',
        'total',
        'estado',
        'observaciones',
        'factura_id',
        'ruta_diaria_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_entrega' => 'date',
        'subtotal' => 'decimal:2',
        'itbms' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function clienteSucursal()
    {
        return $this->belongsTo(ClienteSucursal::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }

    public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function rutaDiaria()
    {
        return $this->belongsTo(RutaDiaria::class);
    }

    public function detalles()
    {
        return $this->hasMany(OrdenEntregaDetalle::class);
    }
}