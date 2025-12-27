<?php

namespace App\Observers;

use App\Models\RutaDiaria;
use App\Services\ContabilidadService;

class RutaDiariaObserver
{
    protected $contabilidadService;

    public function __construct(ContabilidadService $contabilidadService)
    {
        $this->contabilidadService = $contabilidadService;
    }

    public function updated(RutaDiaria $rutaDiaria)
    {
        if ($rutaDiaria->isDirty('estado') && $rutaDiaria->estado === 'liquidada') {
            $this->generarAsientoLiquidacion($rutaDiaria);
        }
    }

    protected function generarAsientoLiquidacion(RutaDiaria $rutaDiaria)
    {
        $cuentaCajaRepartidor = $this->contabilidadService->obtenerCuentaPorSistema($rutaDiaria->empresa_id, 'caja_repartidor');
        $cuentaCajaGeneral = $this->contabilidadService->obtenerCuentaPorSistema($rutaDiaria->empresa_id, 'caja');
        $cuentaBanco = $this->contabilidadService->obtenerCuentaPorSistema($rutaDiaria->empresa_id, 'banco');

        $detalles = [];

        if ($rutaDiaria->total_efectivo > 0) {
            $detalles[] = [
                'cuenta_id' => $cuentaCajaGeneral->id,
                'descripcion' => 'Efectivo liquidado',
                'debito' => $rutaDiaria->total_efectivo,
                'credito' => 0,
            ];
        }

        if ($rutaDiaria->total_transferencias > 0 || $rutaDiaria->total_cheques > 0) {
            $detalles[] = [
                'cuenta_id' => $cuentaBanco->id,
                'descripcion' => 'Transferencias/Cheques',
                'debito' => $rutaDiaria->total_transferencias + $rutaDiaria->total_cheques,
                'credito' => 0,
            ];
        }

        $detalles[] = [
            'cuenta_id' => $cuentaCajaRepartidor->id,
            'descripcion' => 'Cierre caja repartidor',
            'debito' => 0,
            'credito' => $rutaDiaria->total_efectivo + $rutaDiaria->total_transferencias + $rutaDiaria->total_cheques,
        ];

        $this->contabilidadService->crearAsiento([
            'empresa_id' => $rutaDiaria->empresa_id,
            'fecha' => $rutaDiaria->fecha,
            'origen' => 'ruta_diaria',
            'origen_id' => $rutaDiaria->id,
            'concepto' => "Liquidación ruta {$rutaDiaria->numero}",
            'detalles' => $detalles,
        ]);
    }
}