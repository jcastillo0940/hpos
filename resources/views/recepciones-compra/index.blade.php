@extends('layouts.app')

@section('title', 'Recepciones de Compra')
@section('page-title', 'Recepciones de Compra')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Recepciones de Compra</h3>
        <p class="text-slate-600 mt-1">Registra la recepción de productos de tus proveedores</p>
    </div>
    <a href="{{ route('recepciones-compra.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nueva Recepción
    </a>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" placeholder="Buscar por número o proveedor..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition">
            <i class="bi bi-search mr-2"></i>Buscar
        </button>
    </form>
</div>

<!-- Recepciones Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <p class="text-sm text-slate-600">{{ $recepciones->total() }} recepciones encontradas</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Recepción</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orden Compra</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bodega</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recepciones as $recepcion)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-sm font-medium text-slate-700">{{ $recepcion->numero }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $recepcion->fecha ? $recepcion->fecha->format('d/m/Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($recepcion->ordenCompra)
                            <a href="{{ route('ordenes-compra.show', $recepcion->ordenCompra) }}" class="font-medium text-blue-600 hover:text-blue-800">
                                {{ $recepcion->ordenCompra->numero }}
                            </a>
                        @else
                            <span class="text-slate-400">Sin orden</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-slate-800">{{ $recepcion->proveedor->razon_social ?? 'N/A' }}</p>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $recepcion->bodega->nombre ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('recepciones-compra.show', $recepcion) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay recepciones registradas</p>
                            <p class="text-sm">Comienza registrando tu primera recepción de compra</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($recepciones->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $recepciones->links() }}
    </div>
    @endif
</div>
@endsection
