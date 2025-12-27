@extends('layouts.app')

@section('title', 'Órdenes de Entrega')
@section('page-title', 'Órdenes de Entrega')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Órdenes de Entrega</h3>
        <p class="text-slate-600 mt-1">Gestiona las órdenes de tus vendedores</p>
    </div>
    @can('crear_ordenes_entrega')
    <a href="{{ route('ordenes-entrega.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nueva Orden
    </a>
    @endcan
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" placeholder="Buscar por número o cliente..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                <option value="facturada" {{ request('estado') == 'facturada' ? 'selected' : '' }}>Facturada</option>
                <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
            </select>
        </div>
        <div>
            <input type="date" name="fecha" value="{{ request('fecha') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition">
            <i class="bi bi-search mr-2"></i>Buscar
        </button>
    </form>
</div>

<!-- Stats Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Pendientes</p>
                <p class="text-2xl font-bold text-amber-600">{{ $ordenes->where('estado', 'pendiente')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-hourglass-split text-amber-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Aprobadas</p>
                <p class="text-2xl font-bold text-blue-600">{{ $ordenes->where('estado', 'aprobada')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-check-circle text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Facturadas</p>
                <p class="text-2xl font-bold text-green-600">{{ $ordenes->where('estado', 'facturada')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-receipt text-green-600 text-xl"></i>
            </div>
        </div>

    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Total Monto</p>
                <p class="text-2xl font-bold text-slate-800">B/. {{ number_format($ordenes->sum('total'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-cash-stack text-slate-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($ordenes as $orden)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-medium text-blue-600">{{ $orden->numero }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $orden->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="font-medium text-slate-800">{{ $orden->cliente->nombre_comercial }}</p>
                            <p class="text-sm text-slate-500">{{ $orden->cliente->identificacion }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $orden->vendedor->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-bold text-slate-800">B/. {{ number_format($orden->total, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @switch($orden->estado)
                            @case('pendiente')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    <i class="bi bi-hourglass-split mr-1"></i> Pendiente
                                </span>
                                @break
                            @case('aprobada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="bi bi-check-circle mr-1"></i> Aprobada
                                </span>
                                @break
                            @case('facturada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="bi bi-receipt mr-1"></i> Facturada
                                </span>
                                @break
                            @case('anulada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="bi bi-x-circle mr-1"></i> Anulada
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('ordenes-entrega.show', $orden) }}" class="text-blue-600 hover:text-blue-900 transition" title="Ver detalles">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            
                            @if($orden->estado == 'pendiente')
                                @can('convertir_ordenes_entrega')
                                <form method="POST" action="{{ route('ordenes-entrega.aprobar', $orden) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 transition" title="Aprobar">
                                        <i class="bi bi-check-circle text-lg"></i>
                                    </button>
                                </form>
                                @endcan
                                
                                <form method="POST" action="{{ route('ordenes-entrega.anular', $orden) }}" class="inline" onsubmit="return confirm('¿Estás seguro de anular esta orden?')">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Anular">
                                        <i class="bi bi-x-circle text-lg"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay órdenes de entrega</p>
                            <p class="text-sm">Las órdenes aparecerán aquí</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ordenes->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $ordenes->links() }}
    </div>
    @endif
</div>

<!-- Convertir a Factura (Selección Múltiple) -->
@can('convertir_ordenes_entrega')
@if($ordenes->where('estado', 'aprobada')->count() > 0)
<div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-6">
    <h4 class="font-semibold text-blue-900 mb-4 flex items-center">
        <i class="bi bi-lightning-charge mr-2"></i>
        Conversión Masiva a Factura
    </h4>
    <form method="POST" action="{{ route('ordenes-entrega.convertir-factura') }}" x-data="{ selectedOrders: [] }">
        @csrf
        <div class="space-y-2 mb-4 max-h-60 overflow-y-auto">
            @foreach($ordenes->where('estado', 'aprobada') as $orden)
            <label class="flex items-center p-3 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 cursor-pointer transition">
                <input type="checkbox" name="ordenes[]" value="{{ $orden->id }}" x-model="selectedOrders" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                <div class="ml-3 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="font-medium text-slate-800">{{ $orden->numero }} - {{ $orden->cliente->nombre_comercial }}</span>
                        <span class="font-bold text-blue-600">B/. {{ number_format($orden->total, 2) }}</span>
                    </div>
                    <p class="text-xs text-slate-500">{{ $orden->fecha->format('d/m/Y') }}</p>
                </div>
            </label>
            @endforeach
        </div>
        <button type="submit" x-show="selectedOrders.length > 0" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
            <i class="bi bi-receipt mr-2"></i>
            Convertir <span x-text="selectedOrders.length"></span> Orden(es) a Factura
        </button>
    </form>
</div>
@endif
@endcan
@endsection