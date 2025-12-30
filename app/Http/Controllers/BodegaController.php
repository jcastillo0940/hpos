<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BodegaController extends Controller
{
    public function index(Request $request)
    {
        $query = Bodega::with(['empresa', 'repartidor'])
            ->where('empresa_id', Auth::user()->empresa_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('nombre', 'like', "%{$search}%")
                  ->orWhere('responsable', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $bodegas = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('bodegas.index', compact('bodegas'));
    }

    public function create()
    {
        $repartidores = User::where('empresa_id', Auth::user()->empresa_id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'repartidor');
            })
            ->get();

        return view('bodegas.create', compact('repartidores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:bodegas,codigo,NULL,id,empresa_id,' . Auth::user()->empresa_id,
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:Principal,Secundaria,Móvil',
            'placa_vehiculo' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'responsable' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'repartidor_id' => 'nullable|exists:users,id',
            'activa' => 'boolean',
        ]);

        $validated['empresa_id'] = Auth::user()->empresa_id;
        $validated['activa'] = $request->has('activa');

        Bodega::create($validated);

        return redirect()->route('bodegas.index')->with('success', 'Bodega creada exitosamente.');
    }

    public function show(Bodega $bodega)
    {
        $this->authorize('view', $bodega);
        
        $bodega->load(['empresa', 'repartidor']);

        return view('bodegas.show', compact('bodega'));
    }

    public function edit(Bodega $bodega)
    {
        $this->authorize('update', $bodega);

        $repartidores = User::where('empresa_id', Auth::user()->empresa_id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'repartidor');
            })
            ->get();

        return view('bodegas.edit', compact('bodega', 'repartidores'));
    }

    public function update(Request $request, Bodega $bodega)
    {
        $this->authorize('update', $bodega);

        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:bodegas,codigo,' . $bodega->id . ',id,empresa_id,' . Auth::user()->empresa_id,
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:Principal,Secundaria,Móvil',
            'placa_vehiculo' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'responsable' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'repartidor_id' => 'nullable|exists:users,id',
            'activa' => 'boolean',
        ]);

        $validated['activa'] = $request->has('activa');

        $bodega->update($validated);

        return redirect()->route('bodegas.index')->with('success', 'Bodega actualizada exitosamente.');
    }

    public function destroy(Bodega $bodega)
    {
        $this->authorize('delete', $bodega);

        $bodega->delete();

        return redirect()->route('bodegas.index')->with('success', 'Bodega eliminada exitosamente.');
    }
}