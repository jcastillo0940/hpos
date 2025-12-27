@extends('layouts.app')

@section('title', 'Detalle Ruta Diaria')
@section('page-title', 'Ruta Diaria')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $rutaDiaria->numero }}</h3>
                <p class="text-slate-600">{{ $rutaDiaria->fecha->format('d/m/Y') }}</p>
            </div>
            <div class="flex space-x-2">
                @if($rutaDiaria->estado == 'pendiente')
                <form method="POST" action="{{ route('rutas-diarias.iniciar', $rutaDiaria) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class="bi bi-play-circle mr-2"></i>Iniciar Ruta
                    </button>
                </form>
                @endif

                @if($rutaDiaria->estado == 'en_proceso')
                <form method="POST" action="{{ route('rutas-diarias.finalizar', $rutaDiaria) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="bi bi-check-circle mr-2"></i>Finalizar Ruta
                    </button>
                </form>
                @endif
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @switch($rutaDiaria->estado)
                        @case('pendiente') bg-gray-100 text-gray-800 @break
                        @case('en_proceso') bg-blue-100 text-blue-800 @break
                        @case('completada') bg-green-100 text-green-800 @break
                        @case('liquidada') bg-purple-100 text-purple-800 @break
                    @endswitch">
                    {{ ucfirst(str_replace('_', ' ', $rutaDiaria->estado)) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Repartidor</p>
                <p class="font-medium">{{ $rutaDiaria->repartidor->name }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Vehículo</p>
                <p class="font-medium">{{ $rutaDiaria->bodega->placa_vehiculo ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Total Efectivo</p>
                <p class="text-lg font-bold text-green-600">B/. {{ number_format($rutaDiaria->total_efectivo, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Total Ruta</p>
                <p class="text-lg font-bold text-blue-600">B/. {{ number_format($rutaDiaria->total_ruta, 2) }}</p>
            </div>
        </div>

        @if($rutaDiaria->estado == 'liquidada')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
            <div>
                <p class="text-sm text-purple-700">Efectivo Entregado</p>
                <p class="font-bold text-purple-900">B/. {{ number_format($rutaDiaria->efectivo_entregado, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-purple-700">Diferencia</p>
                <p class="font-bold {{ $rutaDiaria->diferencia >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    B/. {{ number_format($rutaDiaria->diferencia, 2) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-purple-700">Liquidado por</p>
                <p class="font-bold text-purple-900">{{ $rutaDiaria->liquidadoPor->name }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <h4 class="font-semibold text-slate-800 mb-4">Entregas</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Orden</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Forma Pago</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($rutaDiaria->detalles as $detalle)
                    <tr>
                        <td class="px-4 py-3">{{ $detalle->orden }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('facturas.show', $detalle->factura) }}" class="text-blue-600 hover:underline">
                                {{ $detalle->factura->numero }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $detalle->cliente->nombre_comercial }}</td>
                        <td class="px-4 py-3 font-bold">B/. {{ number_format($detalle->monto_cobrado, 2) }}</td>
                        <td class="px-4 py-3">{{ $detalle->forma_pago ? ucfirst($detalle->forma_pago) : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @switch($detalle->estado)
                                    @case('pendiente') bg-amber-100 text-amber-800 @break
                                    @case('entregada') bg-green-100 text-green-800 @break
                                    @case('rechazada') bg-red-100 text-red-800 @break
                                    @case('parcial') bg-blue-100 text-blue-800 @break
                                @endswitch">
                                {{ ucfirst($detalle->estado) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
