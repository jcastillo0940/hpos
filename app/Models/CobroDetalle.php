<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CobroDetalle extends Model
{
    protected $table = 'cobros_detalle';

    protected $fillable = [
        'cobro_id', 'factura_id', 'monto_aplicado'
    ];

    protected $casts = [
        'monto_aplicado' => 'decimal:2',
    ];

    public function cobro(): BelongsTo
    {
        return $this->belongsTo(Cobro::class);
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }
}