@extends('layouts.app')

@section('title', 'Clientes')
@section('page-title', 'Clientes')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Gestión de Clientes</h3>
        <p class="text-slate-600 mt-1">Administra tu cartera de clientes</p>
    </div>
    <a href="{{ route('clientes.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nuevo Cliente
    </a>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" placeholder="Buscar por nombre, código o RUC..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition">
            <i class="bi bi-search mr-2"></i>Buscar
        </button>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RUC/Cédula</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($clientes as $cliente)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold">
                                {{ strtoupper(substr($cliente->nombre_comercial, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="font-medium text-slate-800">{{ $cliente->nombre_comercial }}</p>
                                <p class="text-sm text-slate-500">{{ $cliente->codigo }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $cliente->identificacion }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm">
                            <p class="text-slate-800">{{ $cliente->telefono ?? 'N/A' }}</p>
                            <p class="text-slate-500">{{ $cliente->email ?? 'N/A' }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $cliente->vendedor->name ?? 'Sin asignar' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-medium text-slate-800">B/. {{ number_format($cliente->limite_credito, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <span class="font-medium {{ $cliente->saldo_actual > $cliente->limite_credito ? 'text-red-600' : 'text-slate-800' }}">
                            B/. {{ number_format($cliente->saldo_actual, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($cliente->activo)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="bi bi-check-circle mr-1"></i> Activo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="bi bi-dash-circle mr-1"></i> Inactivo
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('clientes.show', $cliente) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('clientes.edit', $cliente) }}" class="text-amber-600 hover:text-amber-900" title="Editar">
                                <i class="bi bi-pencil text-lg"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay clientes registrados</p>
                            <p class="text-sm">Comienza agregando tu primer cliente</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($clientes->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $clientes->links() }}
    </div>
    @endif
</div>
@endsection