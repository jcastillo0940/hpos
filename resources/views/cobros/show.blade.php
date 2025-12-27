@extends('layouts.app')

@section('title', 'Detalle Cobro')
@section('page-title', 'Cobro')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $cobro->numero }}</h3>
                <p class="text-slate-600">{{ $cobro->fecha->format('d/m/Y') }}</p>
            </div>
            <div class="flex space-x-2">
                @if($cobro->estado == 'pendiente')
                <form method="POST" action="{{ route('cobros.aplicar', $cobro) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class="bi bi-check-circle mr-2"></i>Aplicar Cobro
                    </button>
                </form>
                @endif
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    {{ $cobro->estado == 'aplicado' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                    {{ ucfirst($cobro->estado) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Cliente</p>
                <p class="font-medium">{{ $cobro->cliente->nombre_comercial }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Tipo de Pago</p>
                <p class="font-medium">{{ ucfirst($cobro->tipo_pago) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Monto</p>
                <p class="text-2xl font-bold text-green-600">B/. {{ number_format($cobro->monto, 2) }}</p>
            </div>
            @if($cobro->referencia)
            <div>
                <p class="text-sm text-slate-600">Referencia</p>
                <p class="font-medium">{{ $cobro->referencia }}</p>
            </div>
            @endif
            @if($cobro->banco)
            <div>
                <p class="text-sm text-slate-600">Banco</p>
                <p class="font-medium">{{ $cobro->banco }}</p>
            </div>
            @endif
            <div>
                <p class="text-sm text-slate-600">Registrado por</p>
                <p class="font-medium">{{ $cobro->usuario->name }}</p>
            </div>
        </div>

        <div class="mb-6">
            <h4 class="font-semibold text-slate-800 mb-4">Aplicado a Facturas</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Factura</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto Aplicado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($cobro->detalles as $detalle)
                        <tr>
                            <td class="px-4 py-3">
                                <a href="{{ route('facturas.show', $detalle->factura) }}" class="text-blue-600 hover:underline">
                                    {{ $detalle->factura->numero }}
                                </a>
                            </td>
                            <td class="px-4 py-3">{{ $detalle->factura->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">B/. {{ number_format($detalle->factura->total, 2) }}</td>
                            <td class="px-4 py-3 font-bold text-green-600">B/. {{ number_format($detalle->monto_aplicado, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($cobro->observaciones)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm font-medium text-blue-900">Observaciones:</p>
            <p class="text-blue-800">{{ $cobro->observaciones }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
