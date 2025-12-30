<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\PagoDetalle;
use App\Models\Proveedor;
use App\Models\FacturaCompra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    public function index(Request $request)
    {
        $query = Pago::with(['proveedor', 'usuario'])
            ->where('empresa_id', Auth::user()->empresa_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhere('referencia', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', function($q2) use ($search) {
                      $q2->where('razon_social', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pagos = $query->orderBy('fecha', 'desc')->paginate(15);
        $proveedores = Proveedor::where('empresa_id', Auth::user()->empresa_id)
            ->where('activo', true)
            ->get();

        return view('pagos.index', compact('pagos', 'proveedores'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('empresa_id', Auth::user()->empresa_id)
            ->where('activo', true)
            ->get();

        return view('pagos.create', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'proveedor_id' => 'required|exists:proveedores,id',
            'tipo_pago' => 'required|in:efectivo,cheque,transferencia,tarjeta',
            'referencia' => 'nullable|string|max:100',
            'banco' => 'nullable|string|max:100',
            'monto' => 'required|numeric|min:0.01',
            'facturas' => 'required|array|min:1',
            'facturas.*.factura_id' => 'required|exists:facturas_compra,id',
            'facturas.*.monto_aplicado' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            // Validar que el monto total no exceda el monto del pago
            $montoTotal = collect($validated['facturas'])->sum('monto_aplicado');
            
            if ($montoTotal > $validated['monto']) {
                return back()->withErrors(['monto' => 'El monto aplicado excede el monto del pago'])->withInput();
            }

            $pago = Pago::create([
                'empresa_id' => Auth::user()->empresa_id,
                'numero' => $this->generarNumero(),
                'fecha' => $validated['fecha'],
                'proveedor_id' => $validated['proveedor_id'],
                'usuario_id' => Auth::user()->id,
                'tipo_pago' => $validated['tipo_pago'],
                'referencia' => $validated['referencia'] ?? null,
                'banco' => $validated['banco'] ?? null,
                'monto' => $validated['monto'],
                'estado' => 'aplicado',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($validated['facturas'] as $facturaData) {
                PagoDetalle::create([
                    'pago_id' => $pago->id,
                    'factura_compra_id' => $facturaData['factura_id'],
                    'monto_aplicado' => $facturaData['monto_aplicado'],
                ]);

                // Actualizar saldo de la factura
                $factura = FacturaCompra::find($facturaData['factura_id']);
                $factura->decrement('saldo_pendiente', $facturaData['monto_aplicado']);
                
                // Si el saldo llega a 0, marcar como pagada
                if ($factura->fresh()->saldo_pendiente <= 0) {
                    $factura->update(['estado' => 'pagada']);
                }
            }

            // Actualizar saldo del proveedor
            $proveedor = Proveedor::find($validated['proveedor_id']);
            $proveedor->decrement('saldo_actual', $montoTotal);

            return redirect()->route('pagos.show', $pago)
                ->with('success', 'Pago registrado exitosamente');
        });
    }

    public function show($id)
    {
        $pago = Pago::with(['proveedor', 'usuario', 'detalles.facturaCompra'])
            ->findOrFail($id);

        return view('pagos.show', compact('pago'));
    }

    public function aplicar(Pago $pago)
    {
        if ($pago->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Este pago ya fue aplicado');
        }

        DB::transaction(function () use ($pago) {
            foreach ($pago->detalles as $detalle) {
                $factura = $detalle->facturaCompra;
                $factura->decrement('saldo_pendiente', $detalle->monto_aplicado);
                
                if ($factura->fresh()->saldo_pendiente <= 0) {
                    $factura->update(['estado' => 'pagada']);
                }
            }

            $pago->update(['estado' => 'aplicado']);
        });

        return redirect()->back()->with('success', 'Pago aplicado exitosamente');
    }

    public function anular(Pago $pago)
    {
        if ($pago->estado === 'anulado') {
            return redirect()->back()->with('error', 'Este pago ya está anulado');
        }

        DB::transaction(function () use ($pago) {
            // Revertir los montos en las facturas
            foreach ($pago->detalles as $detalle) {
                $factura = $detalle->facturaCompra;
                $factura->increment('saldo_pendiente', $detalle->monto_aplicado);
                $factura->update(['estado' => 'pendiente']);
            }

            // Revertir saldo del proveedor
            $montoTotal = $pago->detalles->sum('monto_aplicado');
            $pago->proveedor->increment('saldo_actual', $montoTotal);

            $pago->update(['estado' => 'anulado']);
        });

        return redirect()->back()->with('success', 'Pago anulado exitosamente');
    }

    public function getFacturasPendientes($proveedorId)
    {
        $facturas = FacturaCompra::where('proveedor_id', $proveedorId)
            ->where('estado', 'pendiente')
            ->where('saldo_pendiente', '>', 0)
            ->get(['id', 'numero_factura', 'fecha', 'total', 'saldo_pendiente']);

        return response()->json($facturas);
    }

    protected function generarNumero()
    {
        $ultimo = Pago::where('empresa_id', Auth::user()->empresa_id)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'PAG-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'PAG-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}