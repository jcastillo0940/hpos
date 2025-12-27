<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zona extends BaseModel
{
    use HasFactory;

    protected $table = 'zonas';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nombre',
        'descripcion',
        'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function rutas()
    {
        return $this->hasMany(Ruta::class);
    }
}