<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proveedor::empresaActual();
        
        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('ruc', 'like', "%{$search}%")
                  ->orWhere('razon_social', 'like', "%{$search}%")
                  ->orWhere('nombre_comercial', 'like', "%{$search}%");
            });
        }
        
        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('activo', $request->estado);
        }
        
        $proveedores = $query->orderBy('nombre_comercial')->paginate(15);
        
        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:proveedores,codigo,NULL,id,empresa_id,' . auth()->user()->empresa_id,
            'ruc' => 'required|string|max:50',
            'dv' => 'nullable|string|max:2',
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'dias_credito' => 'nullable|integer|min:0',
            'contacto_nombre' => 'nullable|string|max:255',
            'contacto_telefono' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
            'activo' => 'nullable|boolean',
        ]);

        $validated['empresa_id'] = auth()->user()->empresa_id;
        $validated['saldo_actual'] = 0;
        $validated['activo'] = $request->has('activo') ? $request->activo : true;

        $proveedor = Proveedor::create($validated);

        return redirect()->route('proveedores.show', $proveedor)
            ->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor)
    {
        // NO verificar empresa_id aquí, Laravel ya lo hace con el binding
        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        // NO verificar empresa_id aquí, Laravel ya lo hace con el binding
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:proveedores,codigo,' . $proveedor->id . ',id,empresa_id,' . auth()->user()->empresa_id,
            'ruc' => 'required|string|max:50',
            'dv' => 'nullable|string|max:2',
            'razon_social' => 'required|string|max:255',
            'nombre_comercial' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:50',
            'direccion' => 'nullable|string',
            'dias_credito' => 'nullable|integer|min:0',
            'contacto_nombre' => 'nullable|string|max:255',
            'contacto_telefono' => 'nullable|string|max:50',
            'observaciones' => 'nullable|string',
            'activo' => 'nullable|boolean',
        ]);

        $validated['activo'] = $request->has('activo') ? $request->activo : false;

        $proveedor->update($validated);

        return redirect()->route('proveedores.show', $proveedor)
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        // Verificar si tiene saldo pendiente
        if ($proveedor->saldo_actual > 0) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No se puede eliminar un proveedor con saldo pendiente.');
        }

        // Verificar si tiene facturas de compra asociadas
        if (method_exists($proveedor, 'facturasCompra') && $proveedor->facturasCompra()->count() > 0) {
            return redirect()->route('proveedores.index')
                ->with('error', 'No se puede eliminar un proveedor con facturas de compra asociadas.');
        }

        $proveedor->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }

    /**
     * Obtener el saldo del proveedor
     */
    public function saldo(Proveedor $proveedor)
    {
        // Obtener facturas de compra pendientes si la relación existe
        $facturasPendientes = [];
        if (method_exists($proveedor, 'facturasCompra')) {
            $facturasPendientes = $proveedor->facturasCompra()
                ->where('saldo_pendiente', '>', 0)
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->with('detalles.producto')
                ->orderBy('fecha')
                ->get();
        }

        // Obtener pagos realizados si la relación existe
        $pagos = [];
        if (method_exists($proveedor, 'pagos')) {
            $pagos = $proveedor->pagos()
                ->where('estado', 'aplicado')
                ->with('detalles.facturaCompra')
                ->orderBy('fecha', 'desc')
                ->take(10)
                ->get();
        }

        return response()->json([
            'proveedor' => $proveedor,
            'facturas_pendientes' => $facturasPendientes,
            'pagos_recientes' => $pagos,
            'saldo_actual' => $proveedor->saldo_actual,
        ]);
    }
}