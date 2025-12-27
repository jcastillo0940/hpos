<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RutaDiariaDetalle extends Model
{
    protected $table = 'rutas_diarias_detalle';

    protected $fillable = [
        'ruta_diaria_id', 'factura_id', 'cliente_id', 'orden', 'estado',
        'fecha_entrega', 'firma_path', 'foto_evidencia', 'forma_pago',
        'monto_cobrado', 'observaciones'
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
        'monto_cobrado' => 'decimal:2',
    ];

    public function rutaDiaria(): BelongsTo
    {
        return $this->belongsTo(RutaDiaria::class);
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }
}