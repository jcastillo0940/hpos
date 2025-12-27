<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Categoria extends BaseModel
{
    use HasFactory;

    protected $table = 'categorias';

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

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}