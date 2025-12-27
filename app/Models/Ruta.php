<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ruta extends BaseModel
{
    use HasFactory;

    protected $table = 'rutas';

    protected $fillable = [
        'empresa_id',
        'zona_id',
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

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }

    public function rutasDiarias()
    {
        return $this->hasMany(RutaDiaria::class);
    }
}