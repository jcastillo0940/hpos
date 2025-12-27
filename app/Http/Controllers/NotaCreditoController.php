<?php

namespace App\Http\Controllers;

use App\Models\NotaCredito;
use App\Models\NotaCreditoDetalle;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaCreditoController extends Controller
{
    public function index()
    {
        $notasCredito = NotaCredito::with(['cliente', 'factura'])
            ->empresaActual()
            ->latest('fecha')
            ->paginate(20);
        
        return view('notas-credito.index', compact('notasCredito'));
    }

    public function create(Request $request)
    {
        $factura = null;
        
        if ($request->has('factura_id')) {
            $factura = Factura::with(['cliente', 'detalles.producto'])
                ->findOrFail($request->factura_id);
        }
        
        return view('notas-credito.create', compact('factura'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'factura_id' => 'required|exists:facturas,id',
            'tipo' => 'required|in:devolucion,merma',
            'motivo' => 'required|in:producto_dañado,producto_vencido,error_facturacion,devolucion_comercial,otro',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.factura_detalle_id' => 'required|exists:facturas_detalle,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($validated) {
            $factura = Factura::with('detalles')->findOrFail($validated['factura_id']);
            
            $subtotal = 0;
            $itbms = 0;
            
            foreach ($validated['detalles'] as $detalle) {
                $facturaDetalle = $factura->detalles->find($detalle['factura_detalle_id']);
                
                $subtotalLinea = $detalle['cantidad'] * $facturaDetalle->precio_unitario;
                $itbmsLinea = $subtotalLinea * ($facturaDetalle->itbms_porcentaje / 100);
                
                $subtotal += $subtotalLinea;
                $itbms += $itbmsLinea;
            }
            
            $notaCredito = NotaCredito::create([
                'empresa_id' => auth()->user()->empresa_id,
                'numero' => $this->generarNumero(),
                'fecha' => now(),
                'factura_id' => $validated['factura_id'],
                'cliente_id' => $factura->cliente_id,
                'tipo' => $validated['tipo'],
                'motivo' => $validated['motivo'],
                'subtotal' => $subtotal,
                'itbms' => $itbms,
                'total' => $subtotal + $itbms,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);
            
            foreach ($validated['detalles'] as $detalle) {
                $facturaDetalle = $factura->detalles->find($detalle['factura_detalle_id']);
                
                $subtotalLinea = $detalle['cantidad'] * $facturaDetalle->precio_unitario;
                $itbmsLinea = $subtotalLinea * ($facturaDetalle->itbms_porcentaje / 100);
                
                NotaCreditoDetalle::create([
                    'nota_credito_id' => $notaCredito->id,
                    'factura_detalle_id' => $detalle['factura_detalle_id'],
                    'producto_id' => $facturaDetalle->producto_id,
                    'bodega_id' => $facturaDetalle->bodega_id,
                    'lote' => $facturaDetalle->lote,
                    'fecha_vencimiento' => $facturaDetalle->fecha_vencimiento,
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $facturaDetalle->precio_unitario,
                    'costo_unitario' => $facturaDetalle->costo_unitario,
                    'itbms_porcentaje' => $facturaDetalle->itbms_porcentaje,
                    'itbms_monto' => $itbmsLinea,
                    'subtotal' => $subtotalLinea,
                    'total' => $subtotalLinea + $itbmsLinea,
                ]);
            }
            
            return redirect()->route('notas-credito.show', $notaCredito)
                ->with('success', 'Nota de crédito creada exitosamente');
        });
    }

    public function show(NotaCredito $notaCredito)
    {
        $notaCredito->load(['cliente', 'factura', 'detalles.producto']);
        
        return view('notas-credito.show', compact('notaCredito'));
    }

    protected function generarNumero()
    {
        $ultimo = NotaCredito::where('empresa_id', auth()->user()->empresa_id)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'NC-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'NC-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}