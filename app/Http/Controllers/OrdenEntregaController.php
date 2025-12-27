<?php

namespace App\Http\Controllers;

use App\Models\OrdenEntrega;
use App\Models\OrdenEntregaDetalle;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdenEntregaController extends Controller
{
    public function index()
    {
        $ordenesEntrega = OrdenEntrega::with(['cliente', 'vendedor', 'clienteSucursal'])
            ->empresaActual()
            ->latest('fecha')
            ->paginate(20);
        
        return view('ordenes-entrega.index', compact('ordenesEntrega'));
    }

    public function create()
    {
        $clientes = Cliente::empresaActual()->where('activo', true)->get();
        $productos = Producto::empresaActual()->where('activo', true)->get();
        $bodegas = Bodega::empresaActual()->where('activa', true)->get();
        
        return view('ordenes-entrega.create', compact('clientes', 'productos', 'bodegas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'cliente_sucursal_id' => 'nullable|exists:clientes_sucursales,id',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $subtotal = 0;
            $itbms = 0;

            foreach ($request->detalles as $detalle) {
                $producto = Producto::find($detalle['producto_id']);
                $subtotalLinea = $detalle['cantidad'] * $detalle['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                
                $subtotal += $subtotalLinea;
                $itbms += $itbmsLinea;
            }

            $total = $subtotal + $itbms;

            $numero = 'OE-' . date('Ymd') . '-' . str_pad(OrdenEntrega::empresaActual()->count() + 1, 5, '0', STR_PAD_LEFT);

            $ordenEntrega = OrdenEntrega::create([
                'empresa_id' => auth()->user()->empresa_id,
                'numero' => $numero,
                'cliente_id' => $validated['cliente_id'],
                'cliente_sucursal_id' => $validated['cliente_sucursal_id'] ?? null,
                'vendedor_id' => auth()->id(),
                'fecha' => $validated['fecha'],
                'subtotal' => $subtotal,
                'itbms' => $itbms,
                'total' => $total,
                'estado' => 'pendiente',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($request->detalles as $detalle) {
                $producto = Producto::find($detalle['producto_id']);
                $subtotalLinea = $detalle['cantidad'] * $detalle['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                $totalLinea = $subtotalLinea + $itbmsLinea;

                OrdenEntregaDetalle::create([
                    'orden_entrega_id' => $ordenEntrega->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'subtotal' => $subtotalLinea,
                    'itbms_porcentaje' => $producto->itbms,
                    'itbms_monto' => $itbmsLinea,
                    'total' => $totalLinea,
                ]);
            }

            return redirect()->route('ordenes-entrega.show', $ordenEntrega)
                ->with('success', 'Orden de entrega creada exitosamente');
        });
    }

    public function show($id)
    {
        $ordenEntrega = OrdenEntrega::with(['cliente', 'clienteSucursal', 'vendedor', 'detalles.producto'])
            ->findOrFail($id);
        
        return view('ordenes-entrega.show', compact('ordenEntrega'));
    }

    public function edit($id)
    {
        $ordenEntrega = OrdenEntrega::findOrFail($id);
        $clientes = Cliente::empresaActual()->where('activo', true)->get();
        $productos = Producto::empresaActual()->where('activo', true)->get();
        $bodegas = Bodega::empresaActual()->where('activa', true)->get();
        
        return view('ordenes-entrega.edit', compact('ordenEntrega', 'clientes', 'productos', 'bodegas'));
    }

    public function update(Request $request, $id)
    {
        $ordenEntrega = OrdenEntrega::findOrFail($id);
        
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'cliente_sucursal_id' => 'nullable|exists:clientes_sucursales,id',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
        ]);

        $ordenEntrega->update($validated);

        return redirect()->route('ordenes-entrega.show', $ordenEntrega)
            ->with('success', 'Orden de entrega actualizada exitosamente');
    }

    public function destroy($id)
    {
        $ordenEntrega = OrdenEntrega::findOrFail($id);
        $ordenEntrega->delete();
        
        return redirect()->route('ordenes-entrega.index')
            ->with('success', 'Orden de entrega eliminada exitosamente');
    }

    public function pdf($id)
    {
        $ordenEntrega = OrdenEntrega::with(['cliente', 'clienteSucursal', 'vendedor', 'detalles.producto', 'empresa'])
            ->findOrFail($id);
        
        $pdf = Pdf::loadView('ordenes-entrega.pdf', compact('ordenEntrega'));
        
        return $pdf->stream('orden-entrega-' . $ordenEntrega->numero . '.pdf');
    }

    public function aprobar($id)
    {
        $ordenEntrega = OrdenEntrega::findOrFail($id);
        $ordenEntrega->update(['estado' => 'aprobada']);
        
        return redirect()->back()->with('success', 'Orden de entrega aprobada');
    }

    public function anular($id)
    {
        $ordenEntrega = OrdenEntrega::findOrFail($id);
        $ordenEntrega->update(['estado' => 'anulada']);
        
        return redirect()->back()->with('success', 'Orden de entrega anulada');
    }

    public function getSucursales(Cliente $cliente)
    {
        $sucursales = $cliente->sucursales()->where('activa', true)->get();
        
        return response()->json($sucursales);
    }

    public function convertirFactura(Request $request)
    {
        $validated = $request->validate([
            'ordenes' => 'required|array|min:1',
            'ordenes.*' => 'exists:ordenes_entrega,id',
        ]);

        return DB::transaction(function () use ($validated) {
            foreach ($validated['ordenes'] as $ordenId) {
                $orden = OrdenEntrega::findOrFail($ordenId);
                
                if ($orden->estado != 'aprobada') {
                    continue;
                }
                
                $orden->update(['estado' => 'facturada']);
            }
            
            return redirect()->back()->with('success', 'Órdenes convertidas a facturas exitosamente');
        });
    }
}