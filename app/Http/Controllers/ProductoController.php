<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::with('categoria')->empresaActual();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_barra', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }
        
        $productos = $query->paginate(20);
        $categorias = Categoria::empresaActual()->where('activa', true)->get();
        
        return view('productos.index', compact('productos', 'categorias'));
    }

    public function create()
    {
        $categorias = Categoria::empresaActual()->where('activa', true)->get();
        
        return view('productos.create', compact('categorias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'codigo_barra' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio_venta' => 'required|numeric|min:0',
            'costo_unitario' => 'required|numeric|min:0',
            'itbms' => 'required|numeric|min:0|max:100',
            'stock_minimo' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $validated['empresa_id'] = auth()->user()->empresa_id;

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente');
    }

    public function show(Producto $producto)
    {
        $producto->load('categoria', 'stocks.bodega');
        
        return view('productos.show', compact('producto'));
    }

    public function edit(Producto $producto)
    {
        $categorias = Categoria::empresaActual()->where('activa', true)->get();
        
        return view('productos.edit', compact('producto', 'categorias'));
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50',
            'codigo_barra' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'nullable|exists:categorias,id',
            'precio_venta' => 'required|numeric|min:0',
            'costo_unitario' => 'required|numeric|min:0',
            'itbms' => 'required|numeric|min:0|max:100',
            'stock_minimo' => 'nullable|numeric|min:0',
            'imagen' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('imagen')) {
            $validated['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente');
    }

    public function stock(Producto $producto)
    {
        $stocks = $producto->stocks()->with('bodega')->get();
        
        $stockTotal = $stocks->sum('cantidad_disponible');
        
        return response()->json([
            'stock_total' => $stockTotal,
            'stocks' => $stocks,
        ]);
    }
}