@extends('layouts.app')

@section('title', 'Proveedores')
@section('page-title', 'Proveedores')

@section('content')
<div class="container mx-auto px-4">
    <!-- Header con botón crear -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Listado de Proveedores</h2>
            <p class="text-slate-600 mt-1">Gestiona tus proveedores y contactos</p>
        </div>
        <a href="{{ route('proveedores.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="bi bi-plus-circle mr-2"></i>Nuevo Proveedor
        </a>
    </div>

    <!-- Filtros y búsqueda -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <form method="GET" action="{{ route('proveedores.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Buscar</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Buscar por código, RUC, nombre..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="1" {{ request('estado') == '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('estado') == '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-search mr-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de proveedores -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">RUC/DV</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Proveedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contacto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Saldo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($proveedores as $proveedor)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-slate-900">{{ $proveedor->codigo }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-slate-600">{{ $proveedor->ruc }}{{ $proveedor->dv ? '-' . $proveedor->dv : '' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-slate-900">{{ $proveedor->nombre_comercial }}</span>
                                @if($proveedor->razon_social != $proveedor->nombre_comercial)
                                <span class="text-xs text-slate-500">{{ $proveedor->razon_social }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                @if($proveedor->contacto_nombre)
                                <span class="text-sm text-slate-700">{{ $proveedor->contacto_nombre }}</span>
                                @endif
                                @if($proveedor->telefono)
                                <span class="text-xs text-slate-500">
                                    <i class="bi bi-telephone text-xs"></i> {{ $proveedor->telefono }}
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium {{ $proveedor->saldo_actual > 0 ? 'text-red-600' : 'text-slate-600' }}">
                                B/. {{ number_format($proveedor->saldo_actual, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($proveedor->activo)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Activo
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Inactivo
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('proveedores.show', $proveedor) }}" 
                                   class="text-blue-600 hover:text-blue-900" 
                                   title="Ver">
                                    <i class="bi bi-eye text-lg"></i>
                                </a>
                                <a href="{{ route('proveedores.edit', $proveedor) }}" 
                                   class="text-amber-600 hover:text-amber-900" 
                                   title="Editar">
                                    <i class="bi bi-pencil text-lg"></i>
                                </a>
                                <form action="{{ route('proveedores.destroy', $proveedor) }}" 
                                      method="POST" 
                                      class="inline"
                                      onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Eliminar">
                                        <i class="bi bi-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="bi bi-inbox text-5xl text-slate-300 mb-3"></i>
                                <p class="text-slate-500">No se encontraron proveedores</p>
                                <a href="{{ route('proveedores.create') }}" class="mt-4 text-blue-600 hover:text-blue-800">
                                    Crear primer proveedor
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($proveedores->hasPages())
        <div class="px-6 py-4 border-t">
            {{ $proveedores->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
