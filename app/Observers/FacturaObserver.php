<?php

namespace App\Observers;

use App\Models\Factura;
use App\Services\ContabilidadService;

class FacturaObserver
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    public function created(Factura $factura)
    {
        $this->generarAsientoVenta($factura);
        $this->generarAsientoCostoVenta($factura);
        $this->descontarInventario($factura);
        $this->actualizarSaldoCliente($factura);
    }

    protected function generarAsientoVenta(Factura $factura)
    {
        $cuentaCxC = $this->contabilidadService->obtenerCuentaPorSistema($factura->empresa_id, 'cxc');
        $cuentaVentas = $this->contabilidadService->obtenerCuentaPorSistema($factura->empresa_id, 'ventas');
        $cuentaITBMS = $this->contabilidadService->obtenerCuentaPorSistema($factura->empresa_id, 'itbms_pagar');

        $this->contabilidadService->crearAsiento([
            'empresa_id' => $factura->empresa_id,
            'fecha' => $factura->fecha,
            'origen' => 'factura',
            'origen_id' => $factura->id,
            'concepto' => "Venta según factura {$factura->numero}",
            'detalles' => [
                [
                    'cuenta_id' => $cuentaCxC->id,
                    'tercero_tipo' => 'cliente',
                    'tercero_id' => $factura->cliente_id,
                    'descripcion' => 'CxC Cliente',
                    'debito' => $factura->total,
                    'credito' => 0,
                ],
                [
                    'cuenta_id' => $cuentaVentas->id,
                    'descripcion' => 'Venta de mercancías',
                    'debito' => 0,
                    'credito' => $factura->subtotal,
                ],
                [
                    'cuenta_id' => $cuentaITBMS->id,
                    'descripcion' => 'ITBMS por pagar',
                    'debito' => 0,
                    'credito' => $factura->itbms,
                ],
            ],
        ]);
    }

    protected function generarAsientoCostoVenta(Factura $factura)
    {
        $cuentaCosto = $this->contabilidadService->obtenerCuentaPorSistema($factura->empresa_id, 'costo_ventas');
        $cuentaInventario = $this->contabilidadService->obtenerCuentaPorSistema($factura->empresa_id, 'inventario');

        $costoTotal = $factura->detalles->sum(function ($detalle) {
            return $detalle->cantidad * $detalle->costo_unitario;
        });

        $this->contabilidadService->crearAsiento([
            'empresa_id' => $factura->empresa_id,
            'fecha' => $factura->fecha,
            'origen' => 'factura',
            'origen_id' => $factura->id,
            'concepto' => "Costo de venta factura {$factura->numero}",
            'detalles' => [
                [
                    'cuenta_id' => $cuentaCosto->id,
                    'descripcion' => 'Costo de mercancías vendidas',
                    'debito' => $costoTotal,
                    'credito' => 0,
                ],
                [
                    'cuenta_id' => $cuentaInventario->id,
                    'descripcion' => 'Salida de inventario',
                    'debito' => 0,
                    'credito' => $costoTotal,
                ],
            ],
        ]);
    }

    protected function descontarInventario(Factura $factura)
    {
        foreach ($factura->detalles as $detalle) {
            $stock = \App\Models\Stock::where('bodega_id', $detalle->bodega_id)
                ->where('producto_id', $detalle->producto_id)
                ->where('lote', $detalle->lote)
                ->first();

            if ($stock) {
                $stock->cantidad -= $detalle->cantidad;
                $stock->cantidad_disponible -= $detalle->cantidad;
                $stock->save();
            }
        }
    }

    protected function actualizarSaldoCliente(Factura $factura)
    {
        $cliente = $factura->cliente;
        $cliente->saldo_actual += $factura->total;
        $cliente->save();
    }
}