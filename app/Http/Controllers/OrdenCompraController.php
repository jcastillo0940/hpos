<?php

namespace App\Http\Controllers;

use App\Models\OrdenCompra;
use App\Models\OrdenCompraDetalle;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Bodega;
use App\Models\OrdenEntrega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenCompraController extends Controller
{
    public function index()
    {
        $ordenesCompra = OrdenCompra::with(['proveedor', 'bodegaDestino'])
            ->empresaActual()
            ->latest('fecha')
            ->paginate(20);
        
        return view('ordenes-compra.index', compact('ordenesCompra'));
    }

    public function create()
    {
        $proveedores = Proveedor::empresaActual()->where('activo', true)->get();
        $productos = Producto::empresaActual()->where('activo', true)->get();
        $bodegas = Bodega::empresaActual()->where('activa', true)->get();
        
        return view('ordenes-compra.create', compact('proveedores', 'productos', 'bodegas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'bodega_destino_id' => 'required|exists:bodegas,id',
            'fecha' => 'required|date',
            'fecha_entrega_esperada' => 'nullable|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad_solicitada' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itbms = 0;
            
            foreach ($validated['detalles'] as $detalle) {
                $producto = Producto::find($detalle['producto_id']);
                $subtotalLinea = $detalle['cantidad_solicitada'] * $detalle['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                
                $subtotal += $subtotalLinea;
                $itbms += $itbmsLinea;
            }
            
            $ordenCompra = OrdenCompra::create([
                'empresa_id' => auth()->user()->empresa_id,
                'numero' => $this->generarNumero(),
                'fecha' => $validated['fecha'],
                'fecha_entrega_esperada' => $validated['fecha_entrega_esperada'] ?? null,
                'proveedor_id' => $validated['proveedor_id'],
                'bodega_destino_id' => $validated['bodega_destino_id'],
                'usuario_id' => auth()->id(),
                'subtotal' => $subtotal,
                'itbms' => $itbms,
                'total' => $subtotal + $itbms,
                'estado' => 'borrador',
            ]);
            
            foreach ($validated['detalles'] as $detalle) {
                $producto = Producto::find($detalle['producto_id']);
                $subtotalLinea = $detalle['cantidad_solicitada'] * $detalle['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                
                OrdenCompraDetalle::create([
                    'orden_compra_id' => $ordenCompra->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad_solicitada' => $detalle['cantidad_solicitada'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'itbms_porcentaje' => $producto->itbms,
                    'itbms_monto' => $itbmsLinea,
                    'subtotal' => $subtotalLinea,
                    'total' => $subtotalLinea + $itbmsLinea,
                ]);
            }
            
            return redirect()->route('ordenes-compra.show', $ordenCompra)
                ->with('success', 'Orden de compra creada exitosamente');
        });
    }

    public function show(OrdenCompra $ordenCompra)
    {
        $ordenCompra->load(['proveedor', 'bodegaDestino', 'usuario', 'detalles.producto']);
        
        return view('ordenes-compra.show', compact('ordenCompra'));
    }

    public function aprobar(OrdenCompra $ordenCompra)
    {
        $ordenCompra->update(['estado' => 'aprobada']);
        
        return redirect()->back()->with('success', 'Orden de compra aprobada');
    }

    public function consolidar(Request $request)
    {
        $validated = $request->validate([
            'ordenes_entrega' => 'required|array|min:1',
            'ordenes_entrega.*' => 'exists:ordenes_entrega,id',
            'proveedor_id' => 'required|exists:proveedores,id',
            'bodega_destino_id' => 'required|exists:bodegas,id',
        ]);

        return DB::transaction(function () use ($validated) {
            $ordenesEntrega = OrdenEntrega::whereIn('id', $validated['ordenes_entrega'])
                ->with('detalles.producto')
                ->get();
            
            $productosConsolidados = [];
            
            foreach ($ordenesEntrega as $orden) {
                foreach ($orden->detalles as $detalle) {
                    $productoId = $detalle->producto_id;
                    
                    if (!isset($productosConsolidados[$productoId])) {
                        $productosConsolidados[$productoId] = [
                            'producto_id' => $productoId,
                            'cantidad' => 0,
                            'precio_unitario' => $detalle->producto->costo_unitario,
                        ];
                    }
                    
                    $productosConsolidados[$productoId]['cantidad'] += $detalle->cantidad;
                }
            }
            
            $subtotal = 0;
            $itbms = 0;
            
            foreach ($productosConsolidados as $prod) {
                $producto = Producto::find($prod['producto_id']);
                $subtotalLinea = $prod['cantidad'] * $prod['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                
                $subtotal += $subtotalLinea;
                $itbms += $itbmsLinea;
            }
            
            $ordenCompra = OrdenCompra::create([
                'empresa_id' => auth()->user()->empresa_id,
                'numero' => $this->generarNumero(),
                'fecha' => now(),
                'proveedor_id' => $validated['proveedor_id'],
                'bodega_destino_id' => $validated['bodega_destino_id'],
                'usuario_id' => auth()->id(),
                'subtotal' => $subtotal,
                'itbms' => $itbms,
                'total' => $subtotal + $itbms,
                'estado' => 'aprobada',
            ]);
            
            foreach ($productosConsolidados as $prod) {
                $producto = Producto::find($prod['producto_id']);
                $subtotalLinea = $prod['cantidad'] * $prod['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                
                OrdenCompraDetalle::create([
                    'orden_compra_id' => $ordenCompra->id,
                    'producto_id' => $prod['producto_id'],
                    'cantidad_solicitada' => $prod['cantidad'],
                    'precio_unitario' => $prod['precio_unitario'],
                    'itbms_porcentaje' => $producto->itbms,
                    'itbms_monto' => $itbmsLinea,
                    'subtotal' => $subtotalLinea,
                    'total' => $subtotalLinea + $itbmsLinea,
                ]);
            }
            
            return redirect()->route('ordenes-compra.show', $ordenCompra)
                ->with('success', 'Orden de compra consolidada exitosamente');
        });
    }

    public function detallesApi($id)
    {
        $ordenCompra = OrdenCompra::with(['detalles.producto'])
            ->findOrFail($id);
        
        return response()->json([
            'orden' => [
                'id' => $ordenCompra->id,
                'numero' => $ordenCompra->numero,
                'proveedor_id' => $ordenCompra->proveedor_id,
                'bodega_destino_id' => $ordenCompra->bodega_destino_id,
            ],
            'detalles' => $ordenCompra->detalles->map(function($detalle) {
                return [
                    'producto_id' => $detalle->producto_id,
                    'cantidad_solicitada' => $detalle->cantidad_solicitada,
                    'cantidad_recibida' => $detalle->cantidad_recibida ?? 0,
                    'precio_unitario' => $detalle->precio_unitario,
                    'producto' => [
                        'id' => $detalle->producto->id,
                        'codigo' => $detalle->producto->codigo,
                        'nombre' => $detalle->producto->nombre,
                    ],
                ];
            })
        ]);
    }

    protected function generarNumero()
    {
        $ultimo = OrdenCompra::where('empresa_id', auth()->user()->empresa_id)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'OC-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'OC-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}