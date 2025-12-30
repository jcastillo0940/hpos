<?php

namespace App\Http\Controllers;

use App\Models\NotaCredito;
use App\Models\NotaCreditoDetalle;
use App\Models\Factura;
use App\Models\Cliente;
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
        // Obtener facturas pendientes/pagadas para el select
        $facturas = Factura::empresaActual()
            ->whereIn('estado', ['pendiente', 'pagada', 'parcial'])
            ->with('cliente')
            ->latest()
            ->get();
        
        // Obtener clientes activos
        $clientes = Cliente::empresaActual()
            ->where('activo', true)
            ->orderBy('nombre_comercial')
            ->get();
        
        $factura = null;
        
        if ($request->has('factura_id')) {
            $factura = Factura::with(['cliente', 'detalles.producto'])
                ->findOrFail($request->factura_id);
        }
        
        return view('notas-credito.create', compact('factura', 'facturas', 'clientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'tipo' => 'required|in:devolucion,descuento,ajuste',
            'motivo_devolucion' => 'required_if:tipo,devolucion|nullable|string',
            'motivo_descuento' => 'required_if:tipo,descuento|nullable|string',
            'motivo_ajuste' => 'required_if:tipo,ajuste|nullable|string',
            'observaciones' => 'required|string',
            'factura_id' => 'nullable|exists:facturas,id',
            'cliente_id' => 'required|exists:clientes,id',
            // Para devoluciones
            'detalles' => 'required_if:tipo,devolucion|array',
            'detalles.*.incluir' => 'nullable',
            'detalles.*.factura_detalle_id' => 'required_with:detalles.*.incluir|exists:facturas_detalle,id',
            'detalles.*.cantidad' => 'required_with:detalles.*.incluir|numeric|min:0.01',
            // Para descuentos y ajustes
            'monto_descuento' => 'required_if:tipo,descuento,ajuste|nullable|numeric|min:0.01',
        ]);

        // Consolidar el motivo según el tipo
        $motivo = $validated['motivo_devolucion'] 
            ?? $validated['motivo_descuento'] 
            ?? $validated['motivo_ajuste'] 
            ?? 'No especificado';

        return DB::transaction(function () use ($validated, $request, $motivo) {
            $factura = null;
            
            if ($request->has('factura_id') && $request->factura_id) {
                $factura = Factura::with('detalles')->findOrFail($validated['factura_id']);
            }
            
            $subtotal = 0;
            $itbms = 0;
            
            // Calcular totales según tipo
            if ($validated['tipo'] === 'devolucion') {
                // Filtrar solo los productos marcados para devolución
                $detallesDevolucion = collect($validated['detalles'] ?? [])
                    ->filter(function($item) {
                        return isset($item['incluir']) && $item['incluir'];
                    });

                if ($detallesDevolucion->isEmpty()) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Debe seleccionar al menos un producto para devolver');
                }

                foreach ($detallesDevolucion as $detalle) {
                    $facturaDetalle = $factura->detalles->find($detalle['factura_detalle_id']);
                    
                    if ($detalle['cantidad'] > $facturaDetalle->cantidad) {
                        return redirect()->back()
                            ->withInput()
                            ->with('error', 'La cantidad a devolver no puede ser mayor a la cantidad facturada');
                    }
                    
                    $subtotalLinea = $detalle['cantidad'] * $facturaDetalle->precio_unitario;
                    $itbmsLinea = $subtotalLinea * ($facturaDetalle->itbms_porcentaje / 100);
                    
                    $subtotal += $subtotalLinea;
                    $itbms += $itbmsLinea;
                }
            } else {
                // Descuento o ajuste directo
                $montoDescuento = $validated['monto_descuento'];
                $subtotal = $montoDescuento / 1.07; // Descontar ITBMS (asumiendo 7%)
                $itbms = $montoDescuento - $subtotal;
            }
            
            // Crear la nota de crédito
            $notaCredito = NotaCredito::create([
                'empresa_id' => auth()->user()->empresa_id,
                'numero' => $this->generarNumero(),
                'fecha' => $validated['fecha'],
                'factura_id' => $validated['factura_id'] ?? null,
                'cliente_id' => $validated['cliente_id'],
                'tipo' => $validated['tipo'],
                'motivo' => $motivo,
                'subtotal' => $subtotal,
                'itbms' => $itbms,
                'total' => $subtotal + $itbms,
                'estado' => 'pendiente',
                'observaciones' => $validated['observaciones'],
            ]);
            
            // Crear detalles si es devolución
            if ($validated['tipo'] === 'devolucion') {
                $detallesDevolucion = collect($validated['detalles'] ?? [])
                    ->filter(function($item) {
                        return isset($item['incluir']) && $item['incluir'];
                    });

                foreach ($detallesDevolucion as $detalle) {
                    $facturaDetalle = $factura->detalles->find($detalle['factura_detalle_id']);
                    
                    $subtotalLinea = $detalle['cantidad'] * $facturaDetalle->precio_unitario;
                    $itbmsLinea = $subtotalLinea * ($facturaDetalle->itbms_porcentaje / 100);
                    
                    NotaCreditoDetalle::create([
                        'nota_credito_id' => $notaCredito->id,
                        'factura_detalle_id' => $detalle['factura_detalle_id'],
                        'producto_id' => $facturaDetalle->producto_id,
                        'bodega_id' => $facturaDetalle->bodega_id ?? null,
                        'lote' => $facturaDetalle->lote ?? null,
                        'fecha_vencimiento' => $facturaDetalle->fecha_vencimiento ?? null,
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $facturaDetalle->precio_unitario,
                        'costo_unitario' => $facturaDetalle->producto->costo ?? 0,
                        'itbms_porcentaje' => $facturaDetalle->itbms_porcentaje,
                        'itbms_monto' => $itbmsLinea,
                        'subtotal' => $subtotalLinea,
                        'total' => $subtotalLinea + $itbmsLinea,
                    ]);
                    
                    // TODO: Integración con inventario
                    // Incrementar stock cuando se aplique la nota
                }
            }
            
            // Actualizar saldo de factura si existe
            if ($factura) {
                $factura->saldo_pendiente = max(0, $factura->saldo_pendiente - $notaCredito->total);
                $factura->save();
            }
            
            // Registrar actividad
            activity()
                ->causedBy(auth()->user())
                ->performedOn($notaCredito)
                ->withProperties([
                    'tipo' => $validated['tipo'],
                    'motivo' => $motivo,
                    'monto' => $notaCredito->total,
                ])
                ->log('Nota de crédito creada');
            
            return redirect()->route('notas-credito.show', $notaCredito)
                ->with('success', 'Nota de crédito creada exitosamente');
        });
    }

    public function show($id)
    {
        $notaCredito = NotaCredito::with(['cliente', 'factura', 'detalles.producto'])
            ->findOrFail($id);
        
        return view('notas-credito.show', compact('notaCredito'));
    }

    public function pdf($id)
    {
        $notaCredito = NotaCredito::with(['cliente', 'factura', 'detalles.producto', 'empresa'])
            ->findOrFail($id);
        
        $pdf = \PDF::loadView('notas-credito.pdf', compact('notaCredito'));
        
        return $pdf->stream("nota-credito-{$notaCredito->numero}.pdf");
    }

    public function aplicar($id)
    {
        $notaCredito = NotaCredito::with('cliente')->findOrFail($id);
        
        if ($notaCredito->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Esta nota de crédito ya fue aplicada o anulada');
        }
        
        DB::transaction(function () use ($notaCredito) {
            // Cambiar estado
            $notaCredito->update(['estado' => 'aplicada']);
            
            // Si es devolución, actualizar inventario
            if ($notaCredito->tipo === 'devolucion') {
                foreach ($notaCredito->detalles as $detalle) {
                    // TODO: Incrementar inventario cuando esté implementado
                    // $inventario = Inventario::firstOrCreate([
                    //     'producto_id' => $detalle->producto_id,
                    //     'bodega_id' => $detalle->bodega_id ?? 1,
                    // ], ['cantidad' => 0]);
                    // $inventario->cantidad += $detalle->cantidad;
                    // $inventario->save();
                }
            }
            
            // Actualizar saldos del cliente
            $notaCredito->cliente->actualizarSaldos();
            
            // TODO: Registrar asiento contable
            // Tipo DEVOLUCION:
            //   DEBE: Devoluciones sobre ventas
            //   HABER: Cuentas por cobrar - Cliente
            // Tipo DESCUENTO:
            //   DEBE: Descuentos otorgados
            //   HABER: Cuentas por cobrar - Cliente
            // Tipo AJUSTE:
            //   DEBE: Ajustes y correcciones
            //   HABER: Cuentas por cobrar - Cliente
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($notaCredito)
                ->log('Nota de crédito aplicada');
        });
        
        return redirect()->back()
            ->with('success', 'Nota de crédito aplicada exitosamente. El saldo del cliente ha sido actualizado.');
    }

    public function anular($id)
    {
        $notaCredito = NotaCredito::with(['factura', 'cliente'])->findOrFail($id);
        
        if ($notaCredito->estado === 'anulada') {
            return redirect()->back()->with('error', 'Esta nota de crédito ya está anulada');
        }
        
        DB::transaction(function () use ($notaCredito) {
            // Revertir saldo en factura si fue aplicada
            if ($notaCredito->estado === 'aplicada' && $notaCredito->factura) {
                $notaCredito->factura->saldo_pendiente += $notaCredito->total;
                $notaCredito->factura->save();
            }
            
            // Si es devolución aplicada, revertir inventario
            if ($notaCredito->estado === 'aplicada' && $notaCredito->tipo === 'devolucion') {
                foreach ($notaCredito->detalles as $detalle) {
                    // TODO: Decrementar inventario
                    // $inventario = Inventario::where('producto_id', $detalle->producto_id)
                    //     ->where('bodega_id', $detalle->bodega_id)
                    //     ->first();
                    // if ($inventario) {
                    //     $inventario->cantidad -= $detalle->cantidad;
                    //     $inventario->save();
                    // }
                }
            }
            
            $notaCredito->update(['estado' => 'anulada']);
            
            // Actualizar saldos del cliente
            //$notaCredito->cliente->actualizarSaldos();
            
            // TODO: Revertir asiento contable
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($notaCredito)
                ->log('Nota de crédito anulada');
        });
        
        return redirect()->back()->with('success', 'Nota de crédito anulada exitosamente');
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