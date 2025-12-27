<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;

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

    public function show(Factura $factura)
    {
        $factura->load(['cliente', 'vendedor', 'detalles.producto']);
        
        return view('facturas.show', compact('factura'));
    }

    public function pdf(Factura $factura)
    {
        $factura->load(['cliente', 'vendedor', 'detalles.producto', 'empresa']);
        
        $pdf = \PDF::loadView('facturas.pdf', compact('factura'));
        
        return $pdf->download("factura-{$factura->numero}.pdf");
    }

    public function anular(Factura $factura)
    {
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
            
            // Reversar saldo cliente
            $cliente = $factura->cliente;
            $cliente->saldo_actual -= $factura->total;
            $cliente->save();
            
            // Anular asientos contables
            $asientos = \App\Models\AsientoContable::where('origen', 'factura')
                ->where('origen_id', $factura->id)
                ->get();
            
            foreach ($asientos as $asiento) {
                $asiento->update(['estado' => 'anulado']);
            }
            
            $factura->update(['estado' => 'anulada']);
        });

        return redirect()->back()->with('success', 'Factura anulada exitosamente');
    }
}