<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteSucursal;
use App\Models\Zona;
use App\Models\Ruta;
use App\Models\ListaPrecio;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with(['vendedor', 'zona', 'listaPrecio'])
            ->empresaActual()
            ->paginate(20);
        
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        $zonas = Zona::empresaActual()->where('activa', true)->get();
        $rutas = Ruta::empresaActual()->where('activa', true)->get();
        $listaPrecios = ListaPrecio::empresaActual()->where('activa', true)->get();
        $vendedores = User::where('empresa_id', auth()->user()->empresa_id)
            ->role('Vendedor')
            ->get();

        return view('clientes.create', compact('zonas', 'rutas', 'listaPrecios', 'vendedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'tipo_identificacion' => 'required|in:ruc,cedula,pasaporte',
            'identificacion' => 'required|string|max:50',
            'nombre_comercial' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'zona_id' => 'nullable|exists:zonas,id',
            'ruta_id' => 'nullable|exists:rutas,id',
            'vendedor_id' => 'nullable|exists:users,id',
            'lista_precio_id' => 'nullable|exists:listas_precios,id',
            'limite_credito' => 'nullable|numeric|min:0',
            'dias_credito' => 'nullable|integer|min:0',
            'sucursales' => 'nullable|array',
            'sucursales.*.codigo' => 'required|string|max:50',
            'sucursales.*.nombre' => 'required|string|max:255',
            'sucursales.*.direccion' => 'required|string',
            'sucursales.*.telefono' => 'nullable|string|max:50',
            'sucursales.*.zona_id' => 'nullable|exists:zonas,id',
            'sucursales.*.ruta_id' => 'nullable|exists:rutas,id',
            'sucursales.*.lista_precio_id' => 'nullable|exists:listas_precios,id',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $cliente = Cliente::create([
                'empresa_id' => auth()->user()->empresa_id,
                'codigo' => $validated['codigo'],
                'tipo_identificacion' => $validated['tipo_identificacion'],
                'identificacion' => $validated['identificacion'],
                'nombre_comercial' => $validated['nombre_comercial'],
                'razon_social' => $validated['razon_social'] ?? $validated['nombre_comercial'],
                'email' => $validated['email'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'zona_id' => $validated['zona_id'] ?? null,
                'ruta_id' => $validated['ruta_id'] ?? null,
                'vendedor_id' => $validated['vendedor_id'] ?? null,
                'lista_precio_id' => $validated['lista_precio_id'] ?? null,
                'limite_credito' => $validated['limite_credito'] ?? 0,
                'dias_credito' => $validated['dias_credito'] ?? 0,
                'activo' => true,
            ]);

            if ($request->has('sucursales')) {
                foreach ($request->sucursales as $sucursal) {
                    ClienteSucursal::create([
                        'empresa_id' => auth()->user()->empresa_id,
                        'cliente_id' => $cliente->id,
                        'codigo' => $sucursal['codigo'],
                        'nombre' => $sucursal['nombre'],
                        'direccion' => $sucursal['direccion'],
                        'telefono' => $sucursal['telefono'] ?? null,
                        'zona_id' => $sucursal['zona_id'] ?? null,
                        'ruta_id' => $sucursal['ruta_id'] ?? null,
                        'lista_precio_id' => $sucursal['lista_precio_id'] ?? null,
                        'activa' => true,
                    ]);
                }
            }

            return redirect()->route('clientes.show', $cliente)
                ->with('success', 'Cliente creado exitosamente');
        });
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['vendedor', 'zona', 'ruta', 'listaPrecio', 'sucursales.zona', 'sucursales.ruta', 'sucursales.listaPrecio']);
        $facturas = $cliente->facturas()->latest()->limit(10)->get();
        
        return view('clientes.show', compact('cliente', 'facturas'));
    }

    public function edit(Cliente $cliente)
    {
        $zonas = Zona::empresaActual()->where('activa', true)->get();
        $rutas = Ruta::empresaActual()->where('activa', true)->get();
        $listaPrecios = ListaPrecio::empresaActual()->where('activa', true)->get();
        $vendedores = User::where('empresa_id', auth()->user()->empresa_id)
            ->role('Vendedor')
            ->get();

        $cliente->load('sucursales');
        
        $sucursalesData = $cliente->sucursales->map(function($s) {
            return [
                'id' => $s->id,
                'codigo' => $s->codigo,
                'nombre' => $s->nombre,
                'direccion' => $s->direccion,
                'telefono' => $s->telefono ?? '',
                'zona_id' => $s->zona_id ?? '',
                'ruta_id' => $s->ruta_id ?? '',
                'lista_precio_id' => $s->lista_precio_id ?? '',
            ];
        })->values();

        return view('clientes.edit', compact('cliente', 'zonas', 'rutas', 'listaPrecios', 'vendedores', 'sucursalesData'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'tipo_identificacion' => 'required|in:ruc,cedula,pasaporte',
            'identificacion' => 'required|string|max:50',
            'nombre_comercial' => 'required|string|max:255',
            'razon_social' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'zona_id' => 'nullable|exists:zonas,id',
            'ruta_id' => 'nullable|exists:rutas,id',
            'vendedor_id' => 'nullable|exists:users,id',
            'lista_precio_id' => 'nullable|exists:listas_precios,id',
            'limite_credito' => 'nullable|numeric|min:0',
            'dias_credito' => 'nullable|integer|min:0',
            'sucursales' => 'nullable|array',
            'sucursales.*.id' => 'nullable|exists:clientes_sucursales,id',
            'sucursales.*.codigo' => 'required|string|max:50',
            'sucursales.*.nombre' => 'required|string|max:255',
            'sucursales.*.direccion' => 'required|string',
            'sucursales.*.telefono' => 'nullable|string|max:50',
            'sucursales.*.zona_id' => 'nullable|exists:zonas,id',
            'sucursales.*.ruta_id' => 'nullable|exists:rutas,id',
            'sucursales.*.lista_precio_id' => 'nullable|exists:listas_precios,id',
        ]);

        return DB::transaction(function () use ($validated, $request, $cliente) {
            $cliente->update([
                'codigo' => $validated['codigo'],
                'tipo_identificacion' => $validated['tipo_identificacion'],
                'identificacion' => $validated['identificacion'],
                'nombre_comercial' => $validated['nombre_comercial'],
                'razon_social' => $validated['razon_social'] ?? $validated['nombre_comercial'],
                'email' => $validated['email'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'zona_id' => $validated['zona_id'] ?? null,
                'ruta_id' => $validated['ruta_id'] ?? null,
                'vendedor_id' => $validated['vendedor_id'] ?? null,
                'lista_precio_id' => $validated['lista_precio_id'] ?? null,
                'limite_credito' => $validated['limite_credito'] ?? 0,
                'dias_credito' => $validated['dias_credito'] ?? 0,
            ]);

            $sucursalesIds = [];
            if ($request->has('sucursales')) {
                foreach ($request->sucursales as $sucursalData) {
                    if (isset($sucursalData['id'])) {
                        $sucursal = ClienteSucursal::find($sucursalData['id']);
                        $sucursal->update([
                            'codigo' => $sucursalData['codigo'],
                            'nombre' => $sucursalData['nombre'],
                            'direccion' => $sucursalData['direccion'],
                            'telefono' => $sucursalData['telefono'] ?? null,
                            'zona_id' => $sucursalData['zona_id'] ?? null,
                            'ruta_id' => $sucursalData['ruta_id'] ?? null,
                            'lista_precio_id' => $sucursalData['lista_precio_id'] ?? null,
                        ]);
                        $sucursalesIds[] = $sucursal->id;
                    } else {
                        $sucursal = ClienteSucursal::create([
                            'empresa_id' => auth()->user()->empresa_id,
                            'cliente_id' => $cliente->id,
                            'codigo' => $sucursalData['codigo'],
                            'nombre' => $sucursalData['nombre'],
                            'direccion' => $sucursalData['direccion'],
                            'telefono' => $sucursalData['telefono'] ?? null,
                            'zona_id' => $sucursalData['zona_id'] ?? null,
                            'ruta_id' => $sucursalData['ruta_id'] ?? null,
                            'lista_precio_id' => $sucursalData['lista_precio_id'] ?? null,
                            'activa' => true,
                        ]);
                        $sucursalesIds[] = $sucursal->id;
                    }
                }
            }

            $cliente->sucursales()->whereNotIn('id', $sucursalesIds)->delete();

            return redirect()->route('clientes.show', $cliente)
                ->with('success', 'Cliente actualizado exitosamente');
        });
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente');
    }

    public function saldo(Cliente $cliente)
    {
        $facturasPendientes = $cliente->facturas()
            ->whereIn('estado', ['pendiente', 'parcial', 'vencida'])
            ->get();

        return response()->json([
            'saldo_actual' => $cliente->saldo_actual,
            'saldo_vencido' => $cliente->saldo_vencido,
            'limite_credito' => $cliente->limite_credito,
            'credito_disponible' => $cliente->limite_credito - $cliente->saldo_actual,
            'facturas_pendientes' => $facturasPendientes,
        ]);
    }
}