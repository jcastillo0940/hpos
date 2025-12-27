<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoDetalle extends Model
{
    protected $table = 'pagos_detalle';

    protected $fillable = [
        'pago_id', 'factura_compra_id', 'monto_aplicado'
    ];

    protected $casts = [
        'monto_aplicado' => 'decimal:2',
    ];

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
    }

    public function facturaCompra(): BelongsTo
    {
        return $this->belongsTo(FacturaCompra::class);
    }
}
}