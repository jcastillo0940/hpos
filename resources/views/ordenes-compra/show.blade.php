@extends('layouts.app')

@section('title', 'Detalle Orden de Compra')
@section('page-title', 'Orden de Compra')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $ordenCompra->numero }}</h3>
                <p class="text-slate-600">{{ $ordenCompra->fecha ? \Carbon\Carbon::parse($ordenCompra->fecha)->format('d/m/Y') : 'Sin fecha' }}</p>
            </div>
            <div class="flex space-x-2">
                @if($ordenCompra->estado == 'borrador')
                <form method="POST" action="{{ route('ordenes-compra.aprobar', $ordenCompra) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class="bi bi-check-circle mr-2"></i>Aprobar
                    </button>
                </form>
                @endif

                @if(in_array($ordenCompra->estado, ['aprobada', 'recibida']))
                <a href="{{ route('facturas-compra.create', ['orden_compra_id' => $ordenCompra->id]) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                    <i class="bi bi-receipt mr-2"></i>Crear Factura
                </a>
                @endif
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @switch($ordenCompra->estado)
                        @case('borrador') bg-gray-100 text-gray-800 @break
                        @case('aprobada') bg-blue-100 text-blue-800 @break
                        @case('recibida') bg-green-100 text-green-800 @break
                        @case('facturada') bg-purple-100 text-purple-800 @break
                        @case('anulada') bg-red-100 text-red-800 @break
                    @endswitch">
                    {{ ucfirst($ordenCompra->estado) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Proveedor</p>
                <p class="font-medium">{{ $ordenCompra->proveedor->razon_social ?? 'Sin proveedor' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Bodega Destino</p>
                <p class="font-medium">{{ $ordenCompra->bodegaDestino->nombre ?? 'Sin bodega' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Total</p>
                <p class="text-2xl font-bold text-blue-600">B/. {{ number_format($ordenCompra->total, 2) }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Solicitado</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Recibido</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($ordenCompra->detalles as $detalle)
                    <tr>
                        <td class="px-4 py-3">{{ $detalle->producto->nombre }}</td>
                        <td class="px-4 py-3">{{ $detalle->cantidad_solicitada }}</td>
                        <td class="px-4 py-3 font-bold {{ $detalle->cantidad_recibida > 0 ? 'text-green-600' : '' }}">
                            {{ $detalle->cantidad_recibida }}
                        </td>
                        <td class="px-4 py-3">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="px-4 py-3 font-bold">B/. {{ number_format($detalle->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-lg">Total:</td>
                        <td class="px-4 py-3 text-xl font-bold text-blue-600">B/. {{ number_format($ordenCompra->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection