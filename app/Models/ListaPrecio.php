<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ListaPrecio extends BaseModel
{
    use HasFactory;

    protected $table = 'listas_precios';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nombre',
        'descripcion',
        'tipo_ajuste',
        'valor_ajuste',
        'activa',
    ];

    protected $casts = [
        'valor_ajuste' => 'decimal:2',
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
}