<?php

namespace App\Http\Controllers;

use App\Models\OrdenEntrega;
use App\Models\OrdenEntregaDetalle;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenEntregaController extends Controller
{
    public function index()
    {
        $ordenes = OrdenEntrega::with(['cliente', 'vendedor'])
            ->empresaActual()
            ->latest('fecha')
            ->paginate(20);
        
        return view('ordenes-entrega.index', compact('ordenes'));
    }

    public function create()
    {
        $clientes = Cliente::empresaActual()->where('activo', true)->get();
        $productos = Producto::empresaActual()->where('activo', true)->get();
        
        return view('ordenes-entrega.create', compact('clientes', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            $numero = $this->generarNumero();
            
            $subtotal = 0;
            $itbms = 0;
            
            foreach ($validated['detalles'] as $detalle) {
                $producto = Producto::find($detalle['producto_id']);
                $subtotalLinea = $detalle['cantidad'] * $detalle['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                
                $subtotal += $subtotalLinea;
                $itbms += $itbmsLinea;
            }
            
            $orden = OrdenEntrega::create([
                'empresa_id' => auth()->user()->empresa_id,
                'numero' => $numero,
                'fecha' => $validated['fecha'],
                'cliente_id' => $validated['cliente_id'],
                'vendedor_id' => auth()->id(),
                'subtotal' => $subtotal,
                'itbms' => $itbms,
                'total' => $subtotal + $itbms,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);
            
            foreach ($validated['detalles'] as $detalle) {
                $producto = Producto::find($detalle['producto_id']);
                $subtotalLinea = $detalle['cantidad'] * $detalle['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                
                OrdenEntregaDetalle::create([
                    'orden_entrega_id' => $orden->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'itbms_porcentaje' => $producto->itbms,
                    'itbms_monto' => $itbmsLinea,
                    'subtotal' => $subtotalLinea,
                    'total' => $subtotalLinea + $itbmsLinea,
                ]);
            }
        });

        return redirect()->route('ordenes-entrega.index')
            ->with('success', 'Orden de entrega creada exitosamente');
    }

    public function show(OrdenEntrega $ordenEntrega)
    {
        $ordenEntrega->load(['cliente', 'vendedor', 'detalles.producto']);
        
        return view('ordenes-entrega.show', compact('ordenEntrega'));
    }

    public function aprobar(OrdenEntrega $ordenEntrega)
    {
        $ordenEntrega->update(['estado' => 'aprobada']);
        
        return redirect()->back()->with('success', 'Orden aprobada');
    }

    public function anular(OrdenEntrega $ordenEntrega)
    {
        $ordenEntrega->update(['estado' => 'anulada']);
        
        return redirect()->back()->with('success', 'Orden anulada');
    }

    public function convertirFactura(Request $request)
    {
        $validated = $request->validate([
            'ordenes' => 'required|array|min:1',
            'ordenes.*' => 'exists:ordenes_entrega,id',
        ]);

        return DB::transaction(function () use ($validated) {
            $ordenes = OrdenEntrega::whereIn('id', $validated['ordenes'])
                ->where('estado', 'aprobada')
                ->with('detalles.producto')
                ->get();
            
            $primeraOrden = $ordenes->first();
            
            $factura = Factura::create([
                'empresa_id' => $primeraOrden->empresa_id,
                'numero' => $this->generarNumeroFactura(),
                'fecha' => now(),
                'cliente_id' => $primeraOrden->cliente_id,
                'vendedor_id' => $primeraOrden->vendedor_id,
                'subtotal' => $ordenes->sum('subtotal'),
                'itbms' => $ordenes->sum('itbms'),
                'total' => $ordenes->sum('total'),
                'tipo_pago' => 'credito',
                'saldo_pendiente' => $ordenes->sum('total'),
            ]);
            
            foreach ($ordenes as $orden) {
                foreach ($orden->detalles as $detalle) {
                    $factura->detalles()->create([
                        'producto_id' => $detalle->producto_id,
                        'cantidad' => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'costo_unitario' => $detalle->producto->costo_unitario,
                        'itbms_porcentaje' => $detalle->itbms_porcentaje,
                        'itbms_monto' => $detalle->itbms_monto,
                        'subtotal' => $detalle->subtotal,
                        'total' => $detalle->total,
                    ]);
                }
                
                $orden->update([
                    'estado' => 'facturada',
                    'factura_id' => $factura->id,
                ]);
            }
            
            return redirect()->route('facturas.show', $factura)
                ->with('success', 'Factura creada exitosamente');
        });
    }

    protected function generarNumero()
    {
        $ultimo = OrdenEntrega::where('empresa_id', auth()->user()->empresa_id)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'OE-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'OE-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }

    protected function generarNumeroFactura()
    {
        $ultimo = Factura::where('empresa_id', auth()->user()->empresa_id)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'FAC-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'FAC-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}