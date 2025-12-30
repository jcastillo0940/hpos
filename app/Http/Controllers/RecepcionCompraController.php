<?php

namespace App\Http\Controllers;

use App\Models\RecepcionCompra;
use App\Models\RecepcionCompraDetalle;
use App\Models\OrdenCompra;
use App\Models\Bodega;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RecepcionCompraController extends Controller
{
    public function index(Request $request)
    {
        $query = RecepcionCompra::with(['ordenCompra', 'proveedor', 'bodega'])
            ->where('empresa_id', Auth::user()->empresa_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', function($q2) use ($search) {
                      $q2->where('razon_social', 'like', "%{$search}%");
                  });
            });
        }

        $recepciones = $query->orderBy('fecha', 'desc')->paginate(15);

        return view('recepciones-compra.index', compact('recepciones'));
    }

    public function create()
    {
        $ordenesCompra = OrdenCompra::where('empresa_id', Auth::user()->empresa_id)
            ->where('estado', 'aprobada')
            ->with(['proveedor', 'detalles.producto'])
            ->get();
        
        $bodegas = Bodega::where('empresa_id', Auth::user()->empresa_id)
            ->where('activa', true)
            ->get();

        return view('recepciones-compra.create', compact('ordenesCompra', 'bodegas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'orden_compra_id' => 'required|exists:ordenes_compra,id',
            'bodega_id' => 'required|exists:bodegas,id',
            'fecha' => 'required|date',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad_recibida' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $ordenCompra = OrdenCompra::find($validated['orden_compra_id']);

            $recepcion = RecepcionCompra::create([
                'empresa_id' => Auth::user()->empresa_id,
                'numero' => $this->generarNumero(),
                'fecha' => $validated['fecha'],
                'orden_compra_id' => $validated['orden_compra_id'],
                'proveedor_id' => $ordenCompra->proveedor_id,
                'bodega_id' => $validated['bodega_id'],
                'usuario_id' => Auth::user()->id,
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($validated['detalles'] as $detalle) {
                if ($detalle['cantidad_recibida'] > 0) {
                    // Buscar el detalle de la orden de compra
                    $detalleOrden = $ordenCompra->detalles()
                        ->where('producto_id', $detalle['producto_id'])
                        ->first();
                    
                    if (!$detalleOrden) {
                        continue; // Si no existe el detalle, saltar
                    }

                    RecepcionCompraDetalle::create([
                        'recepcion_compra_id' => $recepcion->id,
                        'orden_compra_detalle_id' => $detalleOrden->id,
                        'producto_id' => $detalle['producto_id'],
                        'cantidad_recibida' => $detalle['cantidad_recibida'],
                    ]);

                    // Actualizar stock
                    $stock = Stock::firstOrCreate([
                        'producto_id' => $detalle['producto_id'],
                        'bodega_id' => $validated['bodega_id'],
                    ], [
                        'cantidad_disponible' => 0,
                        'cantidad_reservada' => 0,
                    ]);

                    $stock->increment('cantidad_disponible', $detalle['cantidad_recibida']);

                    // Actualizar cantidad recibida en orden de compra
                    $detalleOrden->increment('cantidad_recibida', $detalle['cantidad_recibida']);
                }
            }

            // Actualizar estado de la orden de compra
            $ordenCompra->update(['estado' => 'recibida']);

            return redirect()->route('recepciones-compra.show', $recepcion)
                ->with('success', 'Recepción de compra registrada exitosamente');
        });
    }

    public function show(RecepcionCompra $recepcionCompra)
    {
        $recepcionCompra->load(['ordenCompra', 'proveedor', 'bodega', 'usuario', 'detalles.producto']);

        return view('recepciones-compra.show', compact('recepcionCompra'));
    }

    public function confirmar(RecepcionCompra $recepcionCompra)
    {
        // Lógica adicional de confirmación si es necesaria
        return redirect()->route('recepciones-compra.show', $recepcionCompra)
            ->with('success', 'Recepción confirmada exitosamente');
    }

    protected function generarNumero()
    {
        $ultimo = RecepcionCompra::where('empresa_id', Auth::user()->empresa_id)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'RC-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'RC-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}