@extends('layouts.app')

@section('title', 'Detalle de Bodega')
@section('page-title', 'Detalle de Bodega')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
        <a href="{{ route('bodegas.index') }}" class="hover:text-blue-600">Bodegas</a>
        <i class="bi bi-chevron-right"></i>
        <span class="text-slate-800 font-medium">{{ $bodega->nombre }}</span>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h3 class="text-2xl font-bold text-slate-800">{{ $bodega->nombre }}</h3>
        <div class="flex items-center space-x-2">
            <a href="{{ route('bodegas.edit', $bodega) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-medium rounded-lg transition shadow-sm">
                <i class="bi bi-pencil mr-2"></i>
                Editar
            </a>
            <form action="{{ route('bodegas.destroy', $bodega) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta bodega?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition shadow-sm">
                    <i class="bi bi-trash mr-2"></i>
                    Eliminar
                </button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Info -->
    <div class="lg:col-span-2 space-y-6">
        <!-- General Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                <i class="bi bi-info-circle mr-2 text-blue-600"></i>
                Información General
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Código</p>
                    <p class="font-mono font-semibold text-slate-800">{{ $bodega->codigo }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-slate-500 mb-1">Nombre</p>
                    <p class="font-semibold text-slate-800">{{ $bodega->nombre }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-slate-500 mb-1">Tipo</p>
                    @if($bodega->tipo == 'Principal')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                            <i class="bi bi-star-fill mr-1"></i> Principal
                        </span>
                    @elseif($bodega->tipo == 'Móvil')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                            <i class="bi bi-truck mr-1"></i> Móvil
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Secundaria
                        </span>
                    @endif
                </div>
                
                <div>
                    <p class="text-sm text-slate-500 mb-1">Estado</p>
                    @if($bodega->activa)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="bi bi-check-circle-fill mr-1"></i> Activa
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            <i class="bi bi-x-circle-fill mr-1"></i> Inactiva
                        </span>
                    @endif
                </div>
                
                @if($bodega->placa_vehiculo)
                <div>
                    <p class="text-sm text-slate-500 mb-1">Placa del Vehículo</p>
                    <p class="font-mono font-semibold text-slate-800">{{ $bodega->placa_vehiculo }}</p>
                </div>
                @endif
                
                @if($bodega->direccion)
                <div class="md:col-span-2">
                    <p class="text-sm text-slate-500 mb-1">Dirección</p>
                    <p class="text-slate-800">{{ $bodega->direccion }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Responsable Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                <i class="bi bi-person-badge mr-2 text-blue-600"></i>
                Responsable
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Nombre</p>
                    <p class="font-semibold text-slate-800">{{ $bodega->responsable ?? 'Sin asignar' }}</p>
                </div>
                
                @if($bodega->telefono)
                <div>
                    <p class="text-sm text-slate-500 mb-1">Teléfono</p>
                    <p class="text-slate-800">{{ $bodega->telefono }}</p>
                </div>
                @endif
                
                @if($bodega->repartidor_id)
                <div>
                    <p class="text-sm text-slate-500 mb-1">Repartidor Asignado</p>
                    <p class="font-semibold text-slate-800">{{ $bodega->repartidor->name ?? 'N/A' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Quick Stats Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-sm p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold">Estadísticas Rápidas</h4>
                <i class="bi bi-graph-up text-2xl opacity-75"></i>
            </div>
            
            <div class="space-y-4">
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <p class="text-sm opacity-90 mb-1">Total Productos</p>
                    <p class="text-3xl font-bold">0</p>
                </div>
                
                <div class="bg-white bg-opacity-20 rounded-lg p-4">
                    <p class="text-sm opacity-90 mb-1">Valor Inventario</p>
                    <p class="text-2xl font-bold">B/. 0.00</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                <i class="bi bi-clock-history mr-2 text-blue-600"></i>
                Actividad Reciente
            </h4>
            
            <div class="text-center py-8 text-slate-400">
                <i class="bi bi-inbox text-4xl mb-2"></i>
                <p class="text-sm">No hay actividad reciente</p>
            </div>
        </div>

        <!-- System Info Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
                <i class="bi bi-gear mr-2 text-blue-600"></i>
                Información del Sistema
            </h4>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Creado</span>
                    <span class="font-medium text-slate-800">{{ $bodega->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Última actualización</span>
                    <span class="font-medium text-slate-800">{{ $bodega->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Empresa</span>
                    <span class="font-medium text-slate-800">{{ $bodega->empresa->nombre ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
