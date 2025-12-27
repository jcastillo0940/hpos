<?php

namespace App\Http\Controllers;

use App\Models\Cobro;
use App\Models\CobroDetalle;
use App\Models\Cliente;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CobroController extends Controller
{
    public function index()
    {
        $cobros = Cobro::with(['cliente', 'usuario'])
            ->empresaActual()
            ->latest('fecha')
            ->paginate(20);
        
        return view('cobros.index', compact('cobros'));
    }

    public function create(Request $request)
    {
        $cliente = null;
        $facturasPendientes = collect();
        
        if ($request->has('cliente_id')) {
            $cliente = Cliente::findOrFail($request->cliente_id);
            $facturasPendientes = Factura::where('cliente_id', $cliente->id)
                ->whereIn('estado', ['pendiente', 'parcial', 'vencida'])
                ->get();
        }
        
        $clientes = Cliente::empresaActual()->where('activo', true)->get();
        
        return view('cobros.create', compact('clientes', 'cliente', 'facturasPendientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'tipo_pago' => 'required|in:efectivo,cheque,transferencia,ach',
            'referencia' => 'nullable|string',
            'banco' => 'nullable|string',
            'monto' => 'required|numeric|min:0.01',
            'observaciones' => 'nullable|string',
            'facturas' => 'required|array|min:1',
            'facturas.*.factura_id' => 'required|exists:facturas,id',
            'facturas.*.monto_aplicado' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($validated) {
            $cobro = Cobro::create([
                'empresa_id' => auth()->user()->empresa_id,
                'numero' => $this->generarNumero(),
                'fecha' => $validated['fecha'],
                'cliente_id' => $validated['cliente_id'],
                'usuario_id' => auth()->id(),
                'tipo_pago' => $validated['tipo_pago'],
                'referencia' => $validated['referencia'] ?? null,
                'banco' => $validated['banco'] ?? null,
                'monto' => $validated['monto'],
                'estado' => 'pendiente',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);
            
            foreach ($validated['facturas'] as $facturaData) {
                CobroDetalle::create([
                    'cobro_id' => $cobro->id,
                    'factura_id' => $facturaData['factura_id'],
                    'monto_aplicado' => $facturaData['monto_aplicado'],
                ]);
            }
            
            return redirect()->route('cobros.show', $cobro)
                ->with('success', 'Cobro registrado exitosamente');
        });
    }

    public function show(Cobro $cobro)
    {
        $cobro->load(['cliente', 'usuario', 'detalles.factura']);
        
        return view('cobros.show', compact('cobro'));
    }

    public function aplicar(Cobro $cobro)
    {
        $cobro->update(['estado' => 'aplicado']);
        
        return redirect()->back()->with('success', 'Cobro aplicado exitosamente');
    }

    protected function generarNumero()
    {
        $ultimo = Cobro::where('empresa_id', auth()->user()->empresa_id)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'COB-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'COB-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}