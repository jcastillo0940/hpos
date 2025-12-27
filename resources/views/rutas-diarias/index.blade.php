@extends('layouts.app')

@section('title', 'Rutas Diarias')
@section('page-title', 'Rutas Diarias')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Rutas Diarias de Reparto</h3>
        <p class="text-slate-600 mt-1">Gestiona las rutas de tus repartidores</p>
    </div>
    @can('ver_rutas')
    <a href="{{ route('rutas-diarias.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nueva Ruta
    </a>
    @endcan
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" placeholder="Buscar por número o repartidor..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="en_proceso" {{ request('estado') == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                <option value="completada" {{ request('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                <option value="liquidada" {{ request('estado') == 'liquidada' ? 'selected' : '' }}>Liquidada</option>
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

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">En Proceso</p>
                <p class="text-2xl font-bold text-blue-600">{{ $rutasDiarias->where('estado', 'en_proceso')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-truck text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Completadas Hoy</p>
                <p class="text-2xl font-bold text-green-600">{{ $rutasDiarias->where('estado', 'completada')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Total Efectivo</p>
                <p class="text-2xl font-bold text-slate-800">B/. {{ number_format($rutasDiarias->sum('total_efectivo'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-cash-stack text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Total Ruta</p>
                <p class="text-2xl font-bold text-slate-800">B/. {{ number_format($rutasDiarias->sum('total_ruta'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-currency-dollar text-slate-600 text-xl"></i>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ruta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repartidor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entregas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Efectivo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ruta</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rutasDiarias as $ruta)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-bold text-blue-600">{{ $ruta->numero }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $ruta->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold mr-3">
                                {{ strtoupper(substr($ruta->repartidor->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">{{ $ruta->repartidor->name }}</p>
                                <p class="text-xs text-slate-500">{{ $ruta->bodega->placa_vehiculo ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $ruta->detalles->where('estado', 'entregada')->count() }} entregadas
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                {{ $ruta->detalles->where('estado', 'pendiente')->count() }} pendientes
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-bold text-green-600">B/. {{ number_format($ruta->total_efectivo, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-bold text-slate-800">B/. {{ number_format($ruta->total_ruta, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @switch($ruta->estado)
                            @case('pendiente')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="bi bi-hourglass-split mr-1"></i> Pendiente
                                </span>
                                @break
                            @case('en_proceso')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="bi bi-truck mr-1"></i> En Proceso
                                </span>
                                @break
                            @case('completada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="bi bi-check-circle mr-1"></i> Completada
                                </span>
                                @break
                            @case('liquidada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="bi bi-cash-coin mr-1"></i> Liquidada
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('rutas-diarias.show', $ruta) }}" class="text-blue-600 hover:text-blue-900 transition" title="Ver detalles">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            
                            @if($ruta->estado == 'pendiente')
                                <form method="POST" action="{{ route('rutas-diarias.iniciar', $ruta) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 transition" title="Iniciar ruta">
                                        <i class="bi bi-play-circle text-lg"></i>
                                    </button>
                                </form>
                            @endif

                            @if($ruta->estado == 'en_proceso')
                                <form method="POST" action="{{ route('rutas-diarias.finalizar', $ruta) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:text-amber-900 transition" title="Finalizar ruta">
                                        <i class="bi bi-check-circle text-lg"></i>
                                    </button>
                                </form>
                            @endif

                            @if($ruta->estado == 'completada')
                                <button onclick="openLiquidarModal({{ $ruta->id }}, '{{ $ruta->numero }}', {{ $ruta->total_efectivo }})" class="text-purple-600 hover:text-purple-900 transition" title="Liquidar">
                                    <i class="bi bi-cash-coin text-lg"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay rutas registradas</p>
                            <p class="text-sm">Las rutas diarias aparecerán aquí</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rutasDiarias->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $rutasDiarias->links() }}
    </div>
    @endif
</div>

<!-- Modal Liquidar -->
<div x-data="{ open: false, rutaId: null, rutaNumero: '', totalEfectivo: 0 }" @open-liquidar.window="open = true; rutaId = $event.detail.id; rutaNumero = $event.detail.numero; totalEfectivo = $event.detail.total" x-cloak>
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="open = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-slate-800">Liquidar Ruta</h3>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
                
                <form :action="`/rutas-diarias/${rutaId}/liquidar`" method="POST">
                    @csrf
                    <div class="mb-4">
                        <p class="text-sm text-slate-600 mb-2">Ruta: <span class="font-mono font-bold" x-text="rutaNumero"></span></p>
                        <p class="text-sm text-slate-600 mb-4">Total Efectivo Esperado: <span class="font-bold text-green-600">B/. <span x-text="totalEfectivo.toFixed(2)"></span></span></p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Efectivo Entregado</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-slate-500">B/.</span>
                            <input type="number" step="0.01" name="efectivo_entregado" required class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="0.00">
                        </div>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button" @click="open = false" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 font-medium rounded-lg transition">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                            <i class="bi bi-check-circle mr-2"></i>Liquidar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openLiquidarModal(id, numero, total) {
    window.dispatchEvent(new CustomEvent('open-liquidar', {
        detail: { id, numero, total }
    }));
}
</script>
@endpush
@endsection