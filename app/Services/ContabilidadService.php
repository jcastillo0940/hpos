<?php

namespace App\Services;

use App\Models\AsientoContable;
use App\Models\PlanCuenta;
use Illuminate\Support\Facades\DB;

class ContabilidadService
{
    public function generarNumeroAsiento($empresaId)
    {
        $ultimo = AsientoContable::where('empresa_id', $empresaId)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'ASI-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'ASI-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    public function obtenerCuentaPorSistema($empresaId, $cuentaSistema)
    {
        return PlanCuenta::where('empresa_id', $empresaId)
            ->where('cuenta_sistema', $cuentaSistema)
            ->where('activa', true)
            ->first();
    }

    public function crearAsiento($data)
    {
        return DB::transaction(function () use ($data) {
            $asiento = AsientoContable::create([
                'empresa_id' => $data['empresa_id'],
                'numero' => $this->generarNumeroAsiento($data['empresa_id']),
                'fecha' => $data['fecha'],
                'tipo' => $data['tipo'] ?? 'automatico',
                'origen' => $data['origen'] ?? null,
                'origen_id' => $data['origen_id'] ?? null,
                'concepto' => $data['concepto'],
                'total_debito' => 0,
                'total_credito' => 0,
                'estado' => 'contabilizado',
                'usuario_id' => auth()->id(),
            ]);

            $totalDebito = 0;
            $totalCredito = 0;

            foreach ($data['detalles'] as $detalle) {
                $asiento->detalles()->create([
                    'cuenta_id' => $detalle['cuenta_id'],
                    'tercero_tipo' => $detalle['tercero_tipo'] ?? null,
                    'tercero_id' => $detalle['tercero_id'] ?? null,
                    'descripcion' => $detalle['descripcion'] ?? null,
                    'debito' => $detalle['debito'] ?? 0,
                    'credito' => $detalle['credito'] ?? 0,
                ]);

                $totalDebito += $detalle['debito'] ?? 0;
                $totalCredito += $detalle['credito'] ?? 0;
            }

            $asiento->update([
                'total_debito' => $totalDebito,
                'total_credito' => $totalCredito,
            ]);

            return $asiento;
        });
    }
}