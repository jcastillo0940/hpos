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
            $this->actualizarSaldoCliente($cobro);
        }
    }

    protected function generarAsientoCobro(Cobro $cobro)
    {
        $cuentaCxC = $this->contabilidadService->obtenerCuentaPorSistema($cobro->empresa_id, 'cxc');
        
        $cuentaDestino = match($cobro->tipo_pago) {
            'efectivo' => $this->contabilidadService->obtenerCuentaPorSistema($cobro->empresa_id, 'caja'),
            default => $this->contabilidadService->obtenerCuentaPorSistema($cobro->empresa_id, 'banco'),
        };

        $this->contabilidadService->crearAsiento([
            'empresa_id' => $cobro->empresa_id,
            'fecha' => $cobro->fecha,
            'origen' => 'cobro',
            'origen_id' => $cobro->id,
            'concepto' => "Cobro {$cobro->numero} - {$cobro->tipo_pago}",
            'detalles' => [
                [
                    'cuenta_id' => $cuentaDestino->id,
                    'descripcion' => "Cobro {$cobro->tipo_pago}",
                    'debito' => $cobro->monto,
                    'credito' => 0,
                ],
                [
                    'cuenta_id' => $cuentaCxC->id,
                    'tercero_tipo' => 'cliente',
                    'tercero_id' => $cobro->cliente_id,
                    'descripcion' => 'Disminución CxC',
                    'debito' => 0,
                    'credito' => $cobro->monto,
                ],
            ],
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

    protected function actualizarSaldoCliente(Cobro $cobro)
    {
        $cliente = $cobro->cliente;
        $cliente->saldo_actual -= $cobro->monto;
        $cliente->save();
    }
}