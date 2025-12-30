<?php

namespace App\Http\Controllers;

use App\Models\ListaPrecio;
use App\Models\Producto;
use App\Models\ListaPrecioProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListaPrecioController extends Controller
{
    public function index()
    {
        $listas = ListaPrecio::empresaActual()
            ->withCount('productos')
            ->latest()
            ->paginate(20);
        
        return view('listas-precios.index', compact('listas'));
    }

    public function create()
    {
        return view('listas-precios.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:listas_precios,codigo,NULL,id,empresa_id,' . auth()->user()->empresa_id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'es_default' => 'boolean',
            'activa' => 'boolean',
        ]);

        $validated['empresa_id'] = auth()->user()->empresa_id;

        // Si es default, quitar default de las demás
        if ($request->has('es_default') && $request->es_default) {
            ListaPrecio::where('empresa_id', auth()->user()->empresa_id)
                ->update(['es_default' => false]);
        }

        $lista = ListaPrecio::create($validated);

        return redirect()->route('listas-precios.show', $lista)
            ->with('success', 'Lista de precios creada exitosamente');
    }

    public function show($id)
    {
        $lista = ListaPrecio::with(['productosDetalle.producto'])->findOrFail($id);
        
        $productos = Producto::empresaActual()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();
        
        return view('listas-precios.show', compact('lista', 'productos'));
    }

    public function edit($id)
    {
        $lista = ListaPrecio::findOrFail($id);
        
        return view('listas-precios.edit', compact('lista'));
    }

    public function update(Request $request, $id)
    {
        $lista = ListaPrecio::findOrFail($id);
        
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:listas_precios,codigo,' . $id . ',id,empresa_id,' . auth()->user()->empresa_id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'es_default' => 'boolean',
            'activa' => 'boolean',
        ]);

        // Si es default, quitar default de las demás
        if ($request->has('es_default') && $request->es_default) {
            ListaPrecio::where('empresa_id', auth()->user()->empresa_id)
                ->where('id', '!=', $id)
                ->update(['es_default' => false]);
        }

        $lista->update($validated);

        return redirect()->route('listas-precios.show', $lista)
            ->with('success', 'Lista de precios actualizada exitosamente');
    }

    public function destroy($id)
    {
        $lista = ListaPrecio::findOrFail($id);
        
        if ($lista->clientes()->count() > 0) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar una lista de precios asignada a clientes');
        }
        
        $lista->delete();
        
        return redirect()->route('listas-precios.index')
            ->with('success', 'Lista de precios eliminada exitosamente');
    }

    public function agregarProducto(Request $request, $id)
    {
        $validated = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'tipo_precio' => 'required|in:fijo,porcentaje',
            'precio' => 'required_if:tipo_precio,fijo|nullable|numeric|min:0',
            'porcentaje' => 'required_if:tipo_precio,porcentaje|nullable|numeric',
        ]);

        $lista = ListaPrecio::findOrFail($id);

        // Verificar que no exista ya
        $existe = ListaPrecioProducto::where('lista_precio_id', $id)
            ->where('producto_id', $validated['producto_id'])
            ->exists();

        if ($existe) {
            return redirect()->back()->with('error', 'El producto ya está en esta lista');
        }

        ListaPrecioProducto::create([
            'lista_precio_id' => $id,
            'producto_id' => $validated['producto_id'],
            'tipo_precio' => $validated['tipo_precio'],
            'precio' => $validated['tipo_precio'] === 'fijo' ? $validated['precio'] : null,
            'porcentaje' => $validated['tipo_precio'] === 'porcentaje' ? $validated['porcentaje'] : null,
        ]);

        return redirect()->back()->with('success', 'Producto agregado a la lista');
    }

    public function actualizarProducto(Request $request, $id, $productoId)
    {
        $validated = $request->validate([
            'tipo_precio' => 'required|in:fijo,porcentaje',
            'precio' => 'required_if:tipo_precio,fijo|nullable|numeric|min:0',
            'porcentaje' => 'required_if:tipo_precio,porcentaje|nullable|numeric',
        ]);

        $detalle = ListaPrecioProducto::where('lista_precio_id', $id)
            ->where('producto_id', $productoId)
            ->firstOrFail();

        $detalle->update([
            'tipo_precio' => $validated['tipo_precio'],
            'precio' => $validated['tipo_precio'] === 'fijo' ? $validated['precio'] : null,
            'porcentaje' => $validated['tipo_precio'] === 'porcentaje' ? $validated['porcentaje'] : null,
        ]);

        return redirect()->back()->with('success', 'Precio actualizado');
    }

    public function eliminarProducto($id, $productoId)
    {
        ListaPrecioProducto::where('lista_precio_id', $id)
            ->where('producto_id', $productoId)
            ->delete();

        return redirect()->back()->with('success', 'Producto eliminado de la lista');
    }

    public function recalcularPrecios($id)
    {
        $lista = ListaPrecio::findOrFail($id);
        $lista->recalcularPrecios();

        return redirect()->back()->with('success', 'Precios recalculados exitosamente');
    }

    public function aplicarGlobal(Request $request, $id)
    {
        $validated = $request->validate([
            'tipo_precio' => 'required|in:fijo,porcentaje',
            'porcentaje' => 'required_if:tipo_precio,porcentaje|nullable|numeric',
        ]);

        $lista = ListaPrecio::findOrFail($id);
        
        $productos = Producto::empresaActual()->where('activo', true)->get();

        DB::transaction(function () use ($lista, $productos, $validated) {
            foreach ($productos as $producto) {
                ListaPrecioProducto::updateOrCreate(
                    [
                        'lista_precio_id' => $lista->id,
                        'producto_id' => $producto->id,
                    ],
                    [
                        'tipo_precio' => $validated['tipo_precio'],
                        'precio' => null,
                        'porcentaje' => $validated['tipo_precio'] === 'porcentaje' ? $validated['porcentaje'] : null,
                    ]
                );
            }
        });

        return redirect()->back()->with('success', 'Ajuste aplicado a todos los productos');
    }
}