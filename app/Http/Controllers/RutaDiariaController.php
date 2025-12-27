<?php

namespace App\Http\Controllers;

use App\Models\RutaDiaria;
use App\Models\RutaDiariaDetalle;
use App\Models\Factura;
use App\Models\Ruta;
use App\Models\Bodega;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RutaDiariaController extends Controller
{
    public function index()
    {
        $rutasDiarias = RutaDiaria::with(['repartidor', 'ruta'])
            ->empresaActual()
            ->latest('fecha')
            ->paginate(20);
        
        return view('rutas-diarias.index', compact('rutasDiarias'));
    }

    public function create()
    {
        $repartidores = User::where('empresa_id', auth()->user()->empresa_id)
            ->role('Repartidor')
            ->get();
        
        $bodegas = Bodega::empresaActual()
            ->where('tipo', 'movil')
            ->where('activa', true)
            ->get();
        
        $rutas = Ruta::empresaActual()->where('activa', true)->get();
        
        $facturasPendientes = Factura::empresaActual()
            ->where('estado', 'pendiente')
            ->with('cliente')
            ->get();
        
        return view('rutas-diarias.create', compact('repartidores', 'bodegas', 'rutas', 'facturasPendientes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'repartidor_id' => 'required|exists:users,id',
            'bodega_id' => 'nullable|exists:bodegas,id',
            'ruta_id' => 'nullable|exists:rutas,id',
            'fecha' => 'required|date',
            'facturas' => 'required|array|min:1',
            'facturas.*' => 'exists:facturas,id',
        ]);

        return DB::transaction(function () use ($validated) {
            $rutaDiaria = RutaDiaria::create([
                'empresa_id' => auth()->user()->empresa_id,
                'numero' => $this->generarNumero(),
                'fecha' => $validated['fecha'],
                'repartidor_id' => $validated['repartidor_id'],
                'bodega_id' => $validated['bodega_id'] ?? null,
                'ruta_id' => $validated['ruta_id'] ?? null,
                'estado' => 'pendiente',
            ]);
            
            $orden = 1;
            foreach ($validated['facturas'] as $facturaId) {
                $factura = Factura::find($facturaId);
                
                RutaDiariaDetalle::create([
                    'ruta_diaria_id' => $rutaDiaria->id,
                    'factura_id' => $facturaId,
                    'cliente_id' => $factura->cliente_id,
                    'orden' => $orden++,
                    'estado' => 'pendiente',
                ]);
            }
            
            return redirect()->route('rutas-diarias.show', $rutaDiaria)
                ->with('success', 'Ruta creada exitosamente');
        });
    }

    public function show(RutaDiaria $rutaDiaria)
    {
        $rutaDiaria->load(['repartidor', 'ruta', 'bodega', 'detalles.factura.cliente']);
        
        return view('rutas-diarias.show', compact('rutaDiaria'));
    }

    public function iniciar(RutaDiaria $rutaDiaria)
    {
        $rutaDiaria->update([
            'estado' => 'en_proceso',
            'fecha_inicio' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Ruta iniciada');
    }

    public function finalizar(RutaDiaria $rutaDiaria)
    {
        $rutaDiaria->update([
            'estado' => 'completada',
            'fecha_fin' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Ruta finalizada');
    }

    public function registrarEntrega(Request $request, RutaDiaria $rutaDiaria)
    {
        $validated = $request->validate([
            'detalle_id' => 'required|exists:rutas_diarias_detalle,id',
            'estado' => 'required|in:entregada,rechazada,parcial',
            'forma_pago' => 'nullable|in:efectivo,cheque,transferencia,ach,credito',
            'monto_cobrado' => 'nullable|numeric|min:0',
            'firma' => 'nullable|string',
            'foto_evidencia' => 'nullable|image|max:2048',
        ]);

        $detalle = RutaDiariaDetalle::findOrFail($validated['detalle_id']);
        
        $updateData = [
            'estado' => $validated['estado'],
            'fecha_entrega' => now(),
            'forma_pago' => $validated['forma_pago'] ?? null,
            'monto_cobrado' => $validated['monto_cobrado'] ?? 0,
        ];
        
        if ($request->has('firma')) {
            $firmaPath = $this->guardarFirma($request->firma);
            $updateData['firma_path'] = $firmaPath;
        }
        
        if ($request->hasFile('foto_evidencia')) {
            $updateData['foto_evidencia'] = $request->file('foto_evidencia')->store('evidencias', 'public');
        }
        
        $detalle->update($updateData);
        
        // Actualizar totales de la ruta
        $this->actualizarTotales($rutaDiaria);
        
        return response()->json(['success' => true, 'message' => 'Entrega registrada']);
    }

    public function liquidar(Request $request, RutaDiaria $rutaDiaria)
    {
        $validated = $request->validate([
            'efectivo_entregado' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($rutaDiaria, $validated) {
            $diferencia = $validated['efectivo_entregado'] - $rutaDiaria->total_efectivo;
            
            $rutaDiaria->update([
                'estado' => 'liquidada',
                'fecha_liquidacion' => now(),
                'liquidado_por' => auth()->id(),
                'efectivo_entregado' => $validated['efectivo_entregado'],
                'diferencia' => $diferencia,
            ]);
        });
        
        return redirect()->back()->with('success', 'Ruta liquidada exitosamente');
    }

    protected function actualizarTotales(RutaDiaria $rutaDiaria)
    {
        $detalles = $rutaDiaria->detalles;
        
        $totalEfectivo = $detalles->where('forma_pago', 'efectivo')->sum('monto_cobrado');
        $totalCheques = $detalles->where('forma_pago', 'cheque')->sum('monto_cobrado');
        $totalTransferencias = $detalles->whereIn('forma_pago', ['transferencia', 'ach'])->sum('monto_cobrado');
        $totalCredito = $detalles->where('forma_pago', 'credito')->sum('monto_cobrado');
        
        $rutaDiaria->update([
            'total_efectivo' => $totalEfectivo,
            'total_cheques' => $totalCheques,
            'total_transferencias' => $totalTransferencias,
            'total_credito' => $totalCredito,
            'total_ruta' => $totalEfectivo + $totalCheques + $totalTransferencias + $totalCredito,
        ]);
    }

    protected function guardarFirma($firmaBase64)
    {
        $image = str_replace('data:image/png;base64,', '', $firmaBase64);
        $image = str_replace(' ', '+', $image);
        $imageName = 'firma_' . time() . '.png';
        
        \Storage::disk('public')->put('firmas/' . $imageName, base64_decode($image));
        
        return 'firmas/' . $imageName;
    }

    protected function generarNumero()
    {
        $ultimo = RutaDiaria::where('empresa_id', auth()->user()->empresa_id)
            ->whereYear('fecha', date('Y'))
            ->max('numero');
        
        if (!$ultimo) {
            return 'RUT-' . date('Y') . '-0001';
        }
        
        $partes = explode('-', $ultimo);
        $numero = intval($partes[2]) + 1;
        
        return 'RUT-' . date('Y') . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}