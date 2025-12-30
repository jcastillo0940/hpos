<?php

namespace App\Observers;

use App\Models\Cobro;
use App\Services\ContabilidadService;

class CobroObserver
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    public function created(Cobro $cobro)
    {
        if ($cobro->estado === 'aplicado') {
            $this->generarAsientoCobro($cobro);
            $this->aplicarCobroFacturas($cobro);
            
            // Actualizar saldos usando el método del modelo
            if ($cobro->cliente) {
                $cobro->cliente->actualizarSaldos();
            }
        }
    }

    public function updated(Cobro $cobro)
    {
        // Si cambió el estado, actualizar saldos
        if ($cobro->isDirty('estado')) {
            if ($cobro->estado === 'aplicado') {
                $this->generarAsientoCobro($cobro);
                $this->aplicarCobroFacturas($cobro);
            }
            
            // Actualizar saldos del cliente
            if ($cobro->cliente) {
                $cobro->cliente->actualizarSaldos();
            }
        }
    }

    public function deleted(Cobro $cobro)
    {
        if ($cobro->cliente) {
            $cobro->cliente->actualizarSaldos();
        }
    }

    protected function generarAsientoCobro(Cobro $cobro)
{
    $cuentaCxC = $this->contabilidadService->obtenerCuentaPorSistema($cobro->empresa_id, 'cxc');
    
    $cuentaDestino = match($cobro->tipo_pago) {
        'efectivo' => $this->contabilidadService->obtenerCuentaPorSistema($cobro->empresa_id, 'caja'),
        default => $this->contabilidadService->obtenerCuentaPorSistema($cobro->empresa_id, 'banco'),
    };

    // Calcular monto total de facturas aplicadas
    $montoTotalFacturas = $cobro->detalles->sum('monto_aplicado');

    $detallesAsiento = [
        [
            'cuenta_id' => $cuentaDestino->id,
            'descripcion' => $cobro->es_factoring ? "Cobro factoring {$cobro->tipo_pago} - {$cobro->financiera}" : "Cobro {$cobro->tipo_pago}",
            'debito' => $cobro->monto, // Monto real recibido
            'credito' => 0,
        ],
    ];

    // Si es factoring, agregar el gasto financiero
    if ($cobro->es_factoring && $cobro->descuento_factoring > 0) {
        $cuentaGastoFinanciero = $this->contabilidadService->obtenerCuentaPorSistema($cobro->empresa_id, 'gastos_financieros');
        
        $detallesAsiento[] = [
            'cuenta_id' => $cuentaGastoFinanciero->id,
            'descripcion' => "Descuento factoring ({$cobro->porcentaje_factoring}%) - {$cobro->financiera}",
            'debito' => $cobro->descuento_factoring,
            'credito' => 0,
        ];
    }

    // CxC se reduce por el monto total de las facturas (no por el monto recibido)
    $detallesAsiento[] = [
        'cuenta_id' => $cuentaCxC->id,
        'tercero_tipo' => 'cliente',
        'tercero_id' => $cobro->cliente_id,
        'descripcion' => 'Disminución CxC',
        'debito' => 0,
        'credito' => $montoTotalFacturas, // Total de facturas aplicadas
    ];

    $this->contabilidadService->crearAsiento([
        'empresa_id' => $cobro->empresa_id,
        'fecha' => $cobro->fecha,
        'origen' => 'cobro',
        'origen_id' => $cobro->id,
        'concepto' => $cobro->es_factoring ? 
            "Cobro factoring {$cobro->numero} - {$cobro->financiera} (Desc: {$cobro->porcentaje_factoring}%)" :
            "Cobro {$cobro->numero} - {$cobro->tipo_pago}",
        'detalles' => $detallesAsiento,
    ]);
}

    protected function aplicarCobroFacturas(Cobro $cobro)
    {
        foreach ($cobro->detalles as $detalle) {
            $factura = $detalle->factura;
            $factura->saldo_pendiente -= $detalle->monto_aplicado;
            
            if ($factura->saldo_pendiente <= 0) {
                $factura->estado = 'pagada';
                $factura->saldo_pendiente = 0;
            } else {
                $factura->estado = 'parcial';
            }
            
            $factura->save();
        }
    }
}