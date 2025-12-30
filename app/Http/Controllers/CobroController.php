<?php

namespace App\Http\Controllers;

use App\Models\Cobro;
use App\Models\CobroDetalle;
use App\Models\Cliente;
use App\Models\Factura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                ->where('saldo_pendiente', '>', 0)
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->orderBy('fecha', 'asc')
                ->get();
        }
        
        $clientes = Cliente::empresaActual()
            ->where('activo', true)
            ->orderBy('nombre_comercial')
            ->get();
        
        return view('cobros.create', compact('clientes', 'cliente', 'facturasPendientes'));
    }

   public function store(Request $request)
{
    $validated = $request->validate([
        'cliente_id' => 'required|exists:clientes,id',
        'fecha' => 'required|date',
        'tipo_pago' => 'required|in:efectivo,cheque,transferencia,tarjeta',
        'referencia' => 'nullable|string|max:255',
        'banco' => 'nullable|string|max:255',
        'observaciones' => 'nullable|string',
        'comprobante' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        // Factoring
        'es_factoring' => 'nullable|boolean',
        'porcentaje_factoring' => 'required_if:es_factoring,1|nullable|numeric|min:0|max:100',
        'descuento_factoring' => 'required_if:es_factoring,1|nullable|numeric|min:0',
        'financiera' => 'required_if:es_factoring,1|nullable|string|max:255',
        // Facturas
        'facturas' => 'nullable|array',
        'facturas.*.incluir' => 'nullable',
        'facturas.*.factura_id' => 'required_with:facturas.*.incluir|exists:facturas,id',
        'facturas.*.monto_aplicado' => 'required_with:facturas.*.incluir|numeric|min:0.01',
    ]);

    return DB::transaction(function () use ($validated, $request) {
        // Subir comprobante si existe
        $comprobantePath = null;
        if ($request->hasFile('comprobante')) {
            $comprobantePath = $request->file('comprobante')->store('comprobantes', 'public');
        }

        // Calcular monto total de facturas
        $montoTotalFacturas = 0;
        if (isset($validated['facturas'])) {
            $facturasAplicar = collect($validated['facturas'])
                ->filter(function($item) {
                    return isset($item['incluir']) && $item['incluir'];
                });

            foreach ($facturasAplicar as $facturaData) {
                $montoTotalFacturas += $facturaData['monto_aplicado'];
            }
        }

        // Si es factoring, el monto del cobro es el total menos el descuento
        $esFactoring = $validated['es_factoring'] ?? false;
        $descuentoFactoring = $esFactoring ? ($validated['descuento_factoring'] ?? 0) : 0;
        $porcentajeFactoring = $esFactoring ? ($validated['porcentaje_factoring'] ?? 0) : 0;
        
        // El monto del cobro es lo que realmente se recibe (después del descuento)
        $montoCobro = $esFactoring ? ($montoTotalFacturas - $descuentoFactoring) : $montoTotalFacturas;

        // Crear el cobro
        $cobro = Cobro::create([
            'empresa_id' => auth()->user()->empresa_id,
            'numero' => $this->generarNumero(),
            'fecha' => $validated['fecha'],
            'cliente_id' => $validated['cliente_id'],
            'usuario_id' => auth()->id(),
            'tipo_pago' => $validated['tipo_pago'],
            'es_factoring' => $esFactoring,
            'porcentaje_factoring' => $porcentajeFactoring,
            'descuento_factoring' => $descuentoFactoring,
            'financiera' => $validated['financiera'] ?? null,
            'referencia' => $validated['referencia'] ?? null,
            'banco' => $validated['banco'] ?? null,
            'comprobante_path' => $comprobantePath,
            'monto' => $montoCobro, // Monto real recibido
            'estado' => 'pendiente',
            'observaciones' => $validated['observaciones'] ?? null,
        ]);
        
        // Crear detalles si hay facturas seleccionadas
        if (isset($validated['facturas'])) {
            $facturasAplicar = collect($validated['facturas'])
                ->filter(function($item) {
                    return isset($item['incluir']) && $item['incluir'];
                });

            foreach ($facturasAplicar as $facturaData) {
                CobroDetalle::create([
                    'cobro_id' => $cobro->id,
                    'factura_id' => $facturaData['factura_id'],
                    'monto_aplicado' => $facturaData['monto_aplicado'],
                ]);
            }
        }
        
        // Registrar actividad
        activity()
            ->causedBy(auth()->user())
            ->performedOn($cobro)
            ->withProperties([
                'tipo_pago' => $validated['tipo_pago'],
                'monto_facturas' => $montoTotalFacturas,
                'monto_recibido' => $montoCobro,
                'es_factoring' => $esFactoring,
                'descuento_factoring' => $descuentoFactoring,
            ])
            ->log($esFactoring ? 'Cobro por factoring registrado' : 'Cobro registrado');
        
        return redirect()->route('cobros.show', $cobro)
            ->with('success', $esFactoring ? 
                'Cobro por factoring registrado. Descuento financiero: B/. ' . number_format($descuentoFactoring, 2) : 
                'Cobro registrado exitosamente.');
    });
}

    public function show($id)
    {
        $cobro = Cobro::with(['cliente', 'usuario', 'detalles.factura'])
            ->findOrFail($id);
        
        return view('cobros.show', compact('cobro'));
    }

    public function aplicar($id)
    {
        $cobro = Cobro::with(['detalles.factura', 'cliente'])->findOrFail($id);
        
        if ($cobro->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Este cobro ya fue aplicado o anulado');
        }
        
        DB::transaction(function () use ($cobro) {
            // Aplicar el cobro a cada factura
            foreach ($cobro->detalles as $detalle) {
                $factura = $detalle->factura;
                
                // Reducir saldo pendiente
                $factura->saldo_pendiente = max(0, $factura->saldo_pendiente - $detalle->monto_aplicado);
                
                // Actualizar estado de la factura
                if ($factura->saldo_pendiente == 0) {
                    $factura->estado = 'pagada';
                } elseif ($factura->saldo_pendiente < $factura->total) {
                    $factura->estado = 'parcial';
                }
                
                $factura->save();
            }
            
            // Cambiar estado del cobro
            $cobro->update(['estado' => 'aplicado']);
            
            // Actualizar saldos del cliente
            $cobro->cliente->actualizarSaldos();
            
            // TODO: Registrar asiento contable
            // DEBE: Efectivo/Banco (según tipo de pago)
            // HABER: Cuentas por Cobrar - Cliente
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($cobro)
                ->log('Cobro aplicado a facturas');
        });
        
        return redirect()->back()
            ->with('success', 'Cobro aplicado exitosamente. Los saldos han sido actualizados.');
    }

    public function anular($id)
    {
        $cobro = Cobro::with(['detalles.factura', 'cliente'])->findOrFail($id);
        
        if ($cobro->estado === 'anulado') {
            return redirect()->back()->with('error', 'Este cobro ya está anulado');
        }
        
        DB::transaction(function () use ($cobro) {
            // Si estaba aplicado, revertir saldos
            if ($cobro->estado === 'aplicado') {
                foreach ($cobro->detalles as $detalle) {
                    $factura = $detalle->factura;
                    
                    // Restaurar saldo pendiente
                    $factura->saldo_pendiente += $detalle->monto_aplicado;
                    
                    // Actualizar estado
                    if ($factura->saldo_pendiente >= $factura->total) {
                        $factura->estado = 'pendiente';
                    } else {
                        $factura->estado = 'parcial';
                    }
                    
                    $factura->save();
                }
            }
            
            $cobro->update(['estado' => 'anulado']);
            
            // Actualizar saldos del cliente
           //$cobro->cliente->actualizarSaldos();
            
            // TODO: Revertir asiento contable
            
            activity()
                ->causedBy(auth()->user())
                ->performedOn($cobro)
                ->log('Cobro anulado');
        });
        
        return redirect()->back()->with('success', 'Cobro anulado exitosamente');
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