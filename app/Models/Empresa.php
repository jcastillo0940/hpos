<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Empresa extends BaseModel
{
    protected $fillable = [
        'ruc', 'dv', 'razon_social', 'nombre_comercial', 'email', 
        'telefono', 'direccion', 'provincia', 'distrito', 'corregimiento',
        'logo', 'usa_multibodega', 'usa_lotes', 'usa_vencimientos',
        'metodo_costeo', 'facturacion_electronica', 'pac_proveedor',
        'pac_config', 'activa'
    ];

    protected $casts = [
        'usa_multibodega' => 'boolean',
        'usa_lotes' => 'boolean',
        'usa_vencimientos' => 'boolean',
        'facturacion_electronica' => 'boolean',
        'activa' => 'boolean',
        'pac_config' => 'array',
    ];

    public function bodegas(): HasMany
    {
        return $this->hasMany(Bodega::class);
    }

    public function cuentas(): HasMany
    {
        return $this->hasMany(PlanCuenta::class);
    }

    public function clientes(): HasMany
    {
        return $this->hasMany(Cliente::class);
    }

    public function proveedores(): HasMany
    {
        return $this->hasMany(Proveedor::class);
    }
}