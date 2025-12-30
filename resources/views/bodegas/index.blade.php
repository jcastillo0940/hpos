@extends('layouts.app')

@section('title', 'Bodegas')
@section('page-title', 'Bodegas')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Gestión de Bodegas</h3>
        <p class="text-slate-600 mt-1">Administra tus almacenes y ubicaciones</p>
    </div>
    <a href="{{ route('bodegas.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nueva Bodega
    </a>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" placeholder="Buscar por código, nombre o responsable..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <select name="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los tipos</option>
                <option value="Principal" {{ request('tipo') == 'Principal' ? 'selected' : '' }}>Principal</option>
                <option value="Secundaria" {{ request('tipo') == 'Secundaria' ? 'selected' : '' }}>Secundaria</option>
                <option value="Móvil" {{ request('tipo') == 'Móvil' ? 'selected' : '' }}>Móvil</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition">
            <i class="bi bi-search mr-2"></i>Buscar
        </button>
    </form>
</div>

<!-- Bodegas Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <p class="text-sm text-slate-600">{{ $bodegas->total() }} bodegas encontradas</p>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bodegas as $bodega)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-sm font-medium text-slate-700">{{ $bodega->codigo }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="bi bi-building text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">{{ $bodega->nombre }}</p>
                                @if($bodega->placa_vehiculo)
                                    <p class="text-xs text-slate-500">Placa: {{ $bodega->placa_vehiculo }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($bodega->tipo == 'Principal')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="bi bi-star-fill mr-1"></i> Principal
                            </span>
                        @elseif($bodega->tipo == 'Móvil')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                <i class="bi bi-truck mr-1"></i> Móvil
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                Secundaria
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm">
                            <p class="font-medium text-slate-800">{{ $bodega->responsable ?? 'Sin asignar' }}</p>
                            @if($bodega->telefono)
                                <p class="text-slate-500">{{ $bodega->telefono }}</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-slate-600 max-w-xs truncate">{{ $bodega->direccion ?? 'Sin dirección' }}</p>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($bodega->activa)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Activa
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Inactiva
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('bodegas.show', $bodega) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('bodegas.edit', $bodega) }}" class="text-amber-600 hover:text-amber-900" title="Editar">
                                <i class="bi bi-pencil text-lg"></i>
                            </a>
                            <form action="{{ route('bodegas.destroy', $bodega) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta bodega?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                    <i class="bi bi-trash text-lg"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay bodegas registradas</p>
                            <p class="text-sm">Comienza agregando tu primera bodega</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bodegas->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $bodegas->links() }}
    </div>
    @endif
</div>
@endsection
