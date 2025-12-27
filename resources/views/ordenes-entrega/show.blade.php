@extends('layouts.app')

@section('title', 'Detalle Orden de Entrega')
@section('page-title', 'Orden de Entrega')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $ordenEntrega->numero ?? 'N/A' }}</h3>
                <p class="text-slate-600">{{ $ordenEntrega->fecha ? $ordenEntrega->fecha->format('d/m/Y') : 'Sin fecha' }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('ordenes-entrega.pdf', $ordenEntrega) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="bi bi-file-pdf mr-2"></i>PDF
                </a>
                
                @if($ordenEntrega->estado == 'pendiente')
                    @can('convertir_ordenes_entrega')
                    <form method="POST" action="{{ route('ordenes-entrega.aprobar', $ordenEntrega) }}">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                            <i class="bi bi-check-circle mr-2"></i>Aprobar
                        </button>
                    </form>
                    @endcan
                    
                    <form method="POST" action="{{ route('ordenes-entrega.anular', $ordenEntrega) }}" onsubmit="return confirm('¿Anular esta orden?')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            <i class="bi bi-x-circle mr-2"></i>Anular
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Cliente</p>
                <p class="font-medium">{{ $ordenEntrega->cliente->nombre_comercial ?? 'N/A' }}</p>
            </div>
            
            @if($ordenEntrega->clienteSucursal)
            <div>
                <p class="text-sm text-slate-600">Sucursal</p>
                <p class="font-medium">{{ $ordenEntrega->clienteSucursal->nombre }}</p>
                <p class="text-sm text-slate-500">{{ $ordenEntrega->clienteSucursal->direccion }}</p>
            </div>
            @endif
            
            <div>
                <p class="text-sm text-slate-600">Vendedor</p>
                <p class="font-medium">{{ $ordenEntrega->vendedor->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Estado</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    @switch($ordenEntrega->estado)
                        @case('pendiente') bg-amber-100 text-amber-800 @break
                        @case('aprobada') bg-blue-100 text-blue-800 @break
                        @case('facturada') bg-green-100 text-green-800 @break
                        @case('anulada') bg-red-100 text-red-800 @break
                    @endswitch">
                    {{ ucfirst($ordenEntrega->estado) }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($ordenEntrega->detalles as $detalle)
                    <tr>
                        <td class="px-4 py-3">{{ $detalle->producto->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $detalle->cantidad }}</td>
                        <td class="px-4 py-3">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="px-4 py-3">B/. {{ number_format($detalle->subtotal, 2) }}</td>
                        <td class="px-4 py-3 font-bold">B/. {{ number_format($detalle->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right font-semibold">Total:</td>
                        <td class="px-4 py-3 text-xl font-bold text-blue-600">B/. {{ number_format($ordenEntrega->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection