@extends('layouts.app')

@section('title', 'Detalle Recepción de Compra')
@section('page-title', 'Recepción de Compra')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
            <a href="{{ route('recepciones-compra.index') }}" class="hover:text-blue-600">Recepciones de Compra</a>
            <i class="bi bi-chevron-right"></i>
            <span class="text-slate-800 font-medium">{{ $recepcionCompra->numero }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $recepcionCompra->numero }}</h3>
                <p class="text-slate-600">{{ $recepcionCompra->fecha ? $recepcionCompra->fecha->format('d/m/Y') : 'Sin fecha' }}</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i class="bi bi-check-circle mr-1"></i>
                    Recibida
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Orden de Compra</p>
                @if($recepcionCompra->ordenCompra)
                    <a href="{{ route('ordenes-compra.show', $recepcionCompra->ordenCompra) }}" class="font-medium text-blue-600 hover:text-blue-800">
                        {{ $recepcionCompra->ordenCompra->numero }}
                    </a>
                @else
                    <p class="font-medium text-slate-400">Sin orden de compra</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-slate-600">Proveedor</p>
                <p class="font-medium">{{ $recepcionCompra->proveedor->razon_social ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Bodega</p>
                <p class="font-medium">{{ $recepcionCompra->bodega->nombre ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6 mb-6">
            <h4 class="text-lg font-semibold text-slate-800 mb-4">Productos Recibidos</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad Recibida</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($recepcionCompra->detalles as $detalle)
                        <tr>
                            <td class="px-4 py-3">
                                <div>
                                    <p class="font-medium text-slate-800">{{ $detalle->producto->nombre ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-500">{{ $detalle->producto->codigo ?? '' }}</p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-green-600">{{ $detalle->cantidad_recibida }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-slate-400">
                                No hay productos en esta recepción
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($recepcionCompra->observaciones)
        <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm font-medium text-slate-700 mb-2">Observaciones:</p>
            <p class="text-sm text-slate-600">{{ $recepcionCompra->observaciones }}</p>
        </div>
        @endif

        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-600">Recibido por:</p>
                    <p class="font-medium text-slate-800">{{ $recepcionCompra->usuario->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-slate-600">Fecha de registro:</p>
                    <p class="font-medium text-slate-800">{{ $recepcionCompra->created_at ? $recepcionCompra->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection