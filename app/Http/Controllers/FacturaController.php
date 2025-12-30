<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    public function index()
    {
        $facturas = Factura::with(['cliente', 'vendedor'])
            ->empresaActual()
            ->latest('fecha')
            ->paginate(20);
        
        return view('facturas.index', compact('facturas'));
    }

    public function show($id)
    {
        $factura = Factura::with(['cliente', 'vendedor', 'detalles.producto'])
            ->findOrFail($id);
        
        return view('facturas.show', compact('factura'));
    }

    public function pdf($id)
    {
        $factura = Factura::with(['cliente', 'vendedor', 'detalles.producto', 'empresa'])
            ->findOrFail($id);
        
        $pdf = \PDF::loadView('facturas.pdf', compact('factura'));
        
        return $pdf->stream("factura-{$factura->numero}.pdf");
    }

    public function anular($id)
    {
        $factura = Factura::with(['detalles', 'cliente'])->findOrFail($id);
        
        if ($factura->saldo_pendiente < $factura->total) {
            return redirect()->back()
                ->with('error', 'No se puede anular una factura con pagos aplicados');
        }

        DB::transaction(function () use ($factura) {
            // Reversar inventario
            foreach ($factura->detalles as $detalle) {
                $stock = \App\Models\Stock::where('bodega_id', $detalle->bodega_id)
                    ->where('producto_id', $detalle->producto_id)
                    ->where('lote', $detalle->lote)
                    ->first();
                
                if ($stock) {
                    $stock->cantidad += $detalle->cantidad;
                    $stock->cantidad_disponible += $detalle->cantidad;
                    $stock->save();
                }
            }
            
            // Anular asientos contables si existen
            $asientos = \App\Models\AsientoContable::where('origen', 'factura')
                ->where('origen_id', $factura->id)
                ->get();
            
            foreach ($asientos as $asiento) {
                $asiento->update(['estado' => 'anulado']);
            }
            
            // Anular factura
            $factura->update(['estado' => 'anulada']);
            
            // Actualizar saldos del cliente
           // $factura->cliente->actualizarSaldos();
           // 
            // Registrar actividad
            activity()
                ->causedBy(auth()->user())
                ->performedOn($factura)
                ->log('Factura anulada');
        });

        return redirect()->back()->with('success', 'Factura anulada exitosamente');
    }
}