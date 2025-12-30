@extends('layouts.app')

@section('title', 'Cobro - ' . $cobro->numero)
@section('page-title', 'Detalle de Cobro')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $cobro->numero }}</h3>
                <p class="text-slate-600">{{ $cobro->fecha->format('d/m/Y') }}</p>
            </div>
            <div class="flex space-x-2">
                @if($cobro->estado === 'pendiente')
                <form method="POST" action="{{ route('cobros.aplicar', $cobro) }}" class="inline">
                    @csrf
                    <button type="submit" onclick="return confirm('¿Aplicar este cobro?')" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                        <i class="bi bi-check-circle mr-2"></i>Aplicar Cobro
                    </button>
                </form>
                @endif
                <a href="{{ route('cobros.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                    <i class="bi bi-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-slate-700 mb-3">Información del Cliente</h4>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium text-slate-600">Cliente:</span> {{ $cobro->cliente->nombre_comercial }}</p>
                    <p><span class="font-medium text-slate-600">RUC:</span> {{ $cobro->cliente->identificacion }}</p>
                    <p><span class="font-medium text-slate-600">Teléfono:</span> {{ $cobro->cliente->telefono ?? 'N/A' }}</p>
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-slate-700 mb-3">Información del Pago</h4>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium text-slate-600">Tipo:</span> {{ ucfirst($cobro->tipo_pago) }}</p>
                    
                    @if($cobro->es_factoring)
                    <div class="mt-3 p-3 bg-purple-50 rounded-lg border border-purple-200">
                        <p class="font-semibold text-purple-900 mb-2">
                            <i class="bi bi-building mr-1"></i>Cobro por Factoring
                        </p>
                        <div class="space-y-1 text-xs">
                            <p><span class="font-medium text-purple-700">Financiera:</span> {{ $cobro->financiera }}</p>
                            <p><span class="font-medium text-purple-700">Porcentaje:</span> {{ number_format($cobro->porcentaje_factoring, 2) }}%</p>
                            <p><span class="font-medium text-purple-700">Descuento:</span> <span class="text-red-600 font-bold">B/. {{ number_format($cobro->descuento_factoring, 2) }}</span></p>
                            <div class="pt-2 mt-2 border-t border-purple-300">
                                <p><span class="font-medium text-purple-700">Total Facturas:</span> B/. {{ number_format($cobro->detalles->sum('monto_aplicado'), 2) }}</p>
                                <p><span class="font-medium text-purple-700">Monto Recibido:</span> <span class="text-green-600 font-bold">B/. {{ number_format($cobro->monto, 2) }}</span></p>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    @if($cobro->referencia)
                    <p><span class="font-medium text-slate-600">Referencia:</span> {{ $cobro->referencia }}</p>
                    @endif
                    @if($cobro->banco)
                    <p><span class="font-medium text-slate-600">Banco:</span> {{ $cobro->banco }}</p>
                    @endif
                    <p><span class="font-medium text-slate-600">Registrado por:</span> {{ $cobro->usuario->name }}</p>
                    <p><span class="font-medium text-slate-600">Estado:</span> 
                        @if($cobro->estado === 'pendiente')
                        <span class="text-amber-600 font-semibold">Pendiente</span>
                        @elseif($cobro->estado === 'aplicado')
                        <span class="text-blue-600 font-semibold">Aplicado</span>
                        @else
                        <span class="text-red-600 font-semibold">Anulado</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        @if($cobro->observaciones)
        <div class="mt-4 p-3 bg-gray-50 rounded">
            <p class="text-sm"><span class="font-medium">Observaciones:</span> {{ $cobro->observaciones }}</p>
        </div>
        @endif

        @if($cobro->comprobante_path)
        <div class="mt-4">
            <a href="{{ Storage::url($cobro->comprobante_path) }}" target="_blank" class="inline-flex items-center px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition">
                <i class="bi bi-file-earmark-pdf mr-2"></i>Ver Comprobante
            </a>
        </div>
        @endif
    </div>
    
    <!-- Facturas aplicadas -->
    @if($cobro->detalles->count() > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h4 class="font-semibold text-slate-800">Facturas Aplicadas</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Factura</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Factura</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto Aplicado</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cobro->detalles as $detalle)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('facturas.show', $detalle->factura) }}" class="font-mono text-blue-600 hover:underline">
                                {{ $detalle->factura->numero }}
                            </a>
                        </td>
                        <td class="px-6 py-4">{{ $detalle->factura->fecha->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">B/. {{ number_format($detalle->factura->total, 2) }}</td>
                        <td class="px-6 py-4 font-bold text-green-600">B/. {{ number_format($detalle->monto_aplicado, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                @if($cobro->es_factoring)
                <tfoot class="bg-purple-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-purple-900">
                            Total Facturas:
                        </td>
                        <td class="px-6 py-3 font-bold text-purple-900">
                            B/. {{ number_format($cobro->detalles->sum('monto_aplicado'), 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-red-700">
                            Descuento Factoring ({{ number_format($cobro->porcentaje_factoring, 2) }}%):
                        </td>
                        <td class="px-6 py-3 font-bold text-red-700">
                            - B/. {{ number_format($cobro->descuento_factoring, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-bold text-green-800 text-lg">
                            Monto Real Recibido:
                        </td>
                        <td class="px-6 py-3 font-bold text-green-800 text-lg">
                            B/. {{ number_format($cobro->monto, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    @endif
    
    <!-- Resumen -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-end">
            <div class="w-80 space-y-2">
                @if($cobro->es_factoring)
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="text-sm text-purple-700 mb-2">
                        <i class="bi bi-info-circle mr-1"></i>
                        El gasto por descuento de factoring se registró en contabilidad
                    </p>
                    <div class="text-xs space-y-1">
                        <div class="flex justify-between">
                            <span>Financiera:</span>
                            <span class="font-semibold">{{ $cobro->financiera }}</span>
                        </div>
                        <div class="flex justify-between text-red-600">
                            <span>Gasto Financiero:</span>
                            <span class="font-bold">B/. {{ number_format($cobro->descuento_factoring, 2) }}</span>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="flex justify-between text-lg border-t-2 pt-2">
                    <span class="font-bold text-slate-800">TOTAL COBRADO:</span>
                    <span class="font-bold text-green-600">B/. {{ number_format($cobro->monto, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
