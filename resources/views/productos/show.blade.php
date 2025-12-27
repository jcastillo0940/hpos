@extends('layouts.app')

@section('title', 'Detalle Producto')
@section('page-title', 'Detalle Producto')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div class="flex items-start space-x-4">
                @if($producto->imagen)
                    <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-24 h-24 object-cover rounded-lg" alt="{{ $producto->nombre }}">
                @else
                    <div class="w-24 h-24 bg-slate-200 rounded-lg flex items-center justify-center">
                        <i class="bi bi-box text-slate-400 text-3xl"></i>
                    </div>
                @endif
                <div>
                    <h3 class="text-2xl font-bold text-slate-800">{{ $producto->nombre }}</h3>
                    <p class="text-slate-600">{{ $producto->codigo }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('productos.edit', $producto) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition">
                    <i class="bi bi-pencil mr-2"></i>Editar
                </a>
                <form method="POST" action="{{ route('productos.destroy', $producto) }}" onsubmit="return confirm('¿Estás seguro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="bi bi-trash mr-2"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-slate-600">Precio Venta</p>
                <p class="text-xl font-bold text-blue-600">B/. {{ number_format($producto->precio_venta, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Costo</p>
                <p class="text-xl font-bold text-slate-800">B/. {{ number_format($producto->costo_unitario, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">ITBMS</p>
                <p class="text-xl font-bold text-green-600">{{ $producto->itbms }}%</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Stock Mínimo</p>
                <p class="text-xl font-bold text-amber-600">{{ $producto->stock_minimo }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h4 class="font-semibold text-slate-800 mb-4">Stock por Bodega</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bodega</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Disponible</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reservado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($producto->stocks as $stock)
                    <tr>
                        <td class="px-4 py-2">{{ $stock->bodega->nombre }}</td>
                        <td class="px-4 py-2">{{ $stock->cantidad }}</td>
                        <td class="px-4 py-2 font-bold text-green-600">{{ $stock->cantidad_disponible }}</td>
                        <td class="px-4 py-2 text-amber-600">{{ $stock->cantidad_reservada }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400">No hay stock en bodegas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
