<?php

namespace App\Observers;

use App\Models\NotaCredito;
use App\Services\ContabilidadService;

class NotaCreditoObserver
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    public function created(NotaCredito $notaCredito)
    {
        $this->generarAsientoNotaCredito($notaCredito);
        
        if ($notaCredito->tipo === 'devolucion') {
            $this->reingresarInventario($notaCredito);
            $this->reversarCostoVenta($notaCredito);
        } else {
            $this->registrarMerma($notaCredito);
        }
        
        if ($notaCredito->factura) {
            $this->actualizarSaldoFactura($notaCredito);
        }
        
        // Actualizar saldos usando el método del modelo
        if ($notaCredito->cliente) {
            $notaCredito->cliente->actualizarSaldos();
        }
    }

    public function updated(NotaCredito $notaCredito)
    {
        // Si cambió el estado, actualizar saldos
        if ($notaCredito->isDirty('estado') && $notaCredito->cliente) {
            $notaCredito->cliente->actualizarSaldos();
        }
    }

    public function deleted(NotaCredito $notaCredito)
    {
        if ($notaCredito->cliente) {
            $notaCredito->cliente->actualizarSaldos();
        }
    }

    protected function generarAsientoNotaCredito(NotaCredito $notaCredito)
    {
        $cuentaCxC = $this->contabilidadService->obtenerCuentaPorSistema($notaCredito->empresa_id, 'cxc');
        $cuentaDevolucion = $this->contabilidadService->obtenerCuentaPorSistema($notaCredito->empresa_id, 'devolucion_ventas');
        $cuentaITBMS = $this->contabilidadService->obtenerCuentaPorSistema($notaCredito->empresa_id, 'itbms_pagar');

        $this->contabilidadService->crearAsiento([
            'empresa_id' => $notaCredito->empresa_id,
            'fecha' => $notaCredito->fecha,
            'origen' => 'nota_credito',
            'origen_id' => $notaCredito->id,
            'concepto' => "Nota de crédito {$notaCredito->numero} - Tipo: {$notaCredito->tipo}",
            'detalles' => [
                [
                    'cuenta_id' => $cuentaDevolucion->id,
                    'descripcion' => 'Devolución en ventas',
                    'debito' => $notaCredito->subtotal,
                    'credito' => 0,
                ],
                [
                    'cuenta_id' => $cuentaITBMS->id,
                    'descripcion' => 'Reversión ITBMS',
                    'debito' => $notaCredito->itbms,
                    'credito' => 0,
                ],
                [
                    'cuenta_id' => $cuentaCxC->id,
                    'tercero_tipo' => 'cliente',
                    'tercero_id' => $notaCredito->cliente_id,
                    'descripcion' => 'Disminución CxC',
                    'debito' => 0,
                    'credito' => $notaCredito->total,
                ],
            ],
        ]);
    }

    protected function reingresarInventario(NotaCredito $notaCredito)
    {
        foreach ($notaCredito->detalles as $detalle) {
            $stock = \App\Models\Stock::firstOrCreate(
                [
                    'empresa_id' => $notaCredito->empresa_id,
                    'bodega_id' => $detalle->bodega_id,
                    'producto_id' => $detalle->producto_id,
                    'lote' => $detalle->lote,
                    'fecha_vencimiento' => $detalle->fecha_vencimiento,
                ],
                [
                    'cantidad' => 0,
                    'cantidad_reservada' => 0,
                    'cantidad_disponible' => 0,
                    'costo_unitario' => $detalle->costo_unitario,
                ]
            );

            $stock->cantidad += $detalle->cantidad;
            $stock->cantidad_disponible += $detalle->cantidad;
            $stock->save();
        }
    }

    protected function reversarCostoVenta(NotaCredito $notaCredito)
    {
        $cuentaCosto = $this->contabilidadService->obtenerCuentaPorSistema($notaCredito->empresa_id, 'costo_ventas');
        $cuentaInventario = $this->contabilidadService->obtenerCuentaPorSistema($notaCredito->empresa_id, 'inventario');

        $costoTotal = $notaCredito->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->costo_unitario;
        });

        $this->contabilidadService->crearAsiento([
            'empresa_id' => $notaCredito->empresa_id,
            'fecha' => $notaCredito->fecha,
            'origen' => 'nota_credito',
            'origen_id' => $notaCredito->id,
            'concepto' => "Reversión costo NC {$notaCredito->numero}",
            'detalles' => [
                [
                    'cuenta_id' => $cuentaInventario->id,
                    'descripcion' => 'Reingreso a inventario',
                    'debito' => $costoTotal,
                    'credito' => 0,
                ],
                [
                    'cuenta_id' => $cuentaCosto->id,
                    'descripcion' => 'Reversión costo de venta',
                    'debito' => 0,
                    'credito' => $costoTotal,
                ],
            ],
        ]);
    }

    protected function registrarMerma(NotaCredito $notaCredito)
    {
        $cuentaGastoMerma = $this->contabilidadService->obtenerCuentaPorSistema($notaCredito->empresa_id, 'gasto_mermas');
        $cuentaInventario = $this->contabilidadService->obtenerCuentaPorSistema($notaCredito->empresa_id, 'inventario');

        $costoTotal = $notaCredito->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->costo_unitario;
        });

        $this->contabilidadService->crearAsiento([
            'empresa_id' => $notaCredito->empresa_id,
            'fecha' => $notaCredito->fecha,
            'origen' => 'nota_credito',
            'origen_id' => $notaCredito->id,
            'concepto' => "Gasto por merma NC {$notaCredito->numero}",
            'detalles' => [
                [
                    'cuenta_id' => $cuentaGastoMerma->id,
                    'descripcion' => 'Pérdida por merma/vencido',
                    'debito' => $costoTotal,
                    'credito' => 0,
                ],
                [
                    'cuenta_id' => $cuentaInventario->id,
                    'descripcion' => 'Baja de inventario',
                    'debito' => 0,
                    'credito' => $costoTotal,
                ],
            ],
        ]);
    }

    protected function actualizarSaldoFactura(NotaCredito $notaCredito)
    {
        $factura = $notaCredito->factura;
        $factura->saldo_pendiente -= $notaCredito->total;
        
        if ($factura->saldo_pendiente <= 0) {
            $factura->estado = 'pagada';
            $factura->saldo_pendiente = 0;
        } else {
            $factura->estado = 'parcial';
        }
        
        $factura->save();
    }
}