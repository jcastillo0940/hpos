@extends('layouts.app')

@section('title', 'Órdenes de Compra')
@section('page-title', 'Órdenes de Compra')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Órdenes de Compra</h3>
        <p class="text-slate-600 mt-1">Gestiona las órdenes de compra a proveedores</p>
    </div>
    @can('ver_ordenes_compra')
    <a href="{{ route('ordenes-compra.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nueva Orden de Compra
    </a>
    @endcan
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" placeholder="Buscar por número o proveedor..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                <option value="recibida" {{ request('estado') == 'recibida' ? 'selected' : '' }}>Recibida</option>
                <option value="facturada" {{ request('estado') == 'facturada' ? 'selected' : '' }}>Facturada</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition">
            <i class="bi bi-search mr-2"></i>Buscar
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proveedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bodega Destino</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($ordenesCompra as $orden)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-medium text-blue-600">{{ $orden->numero }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $orden->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="font-medium text-slate-800">{{ $orden->proveedor->razon_social }}</p>
                            <p class="text-sm text-slate-500">{{ $orden->proveedor->identificacion }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $orden->bodegaDestino->nombre }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-bold text-slate-800">B/. {{ number_format($orden->total, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @switch($orden->estado)
                            @case('borrador')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="bi bi-pencil mr-1"></i> Borrador
                                </span>
                                @break
                            @case('aprobada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="bi bi-check-circle mr-1"></i> Aprobada
                                </span>
                                @break
                            @case('recibida')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="bi bi-box-seam mr-1"></i> Recibida
                                </span>
                                @break
                            @case('facturada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <i class="bi bi-receipt mr-1"></i> Facturada
                                </span>
                                @break
                        @endswitch
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('ordenes-compra.show', $orden) }}" class="text-blue-600 hover:text-blue-900 transition" title="Ver detalles">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            
                            @if($orden->estado == 'borrador')
                                @can('crear_ordenes_compra')
                                <form method="POST" action="{{ route('ordenes-compra.aprobar', $orden) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 transition" title="Aprobar">
                                        <i class="bi bi-check-circle text-lg"></i>
                                    </button>
                                </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay órdenes de compra</p>
                            <p class="text-sm">Las órdenes aparecerán aquí</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ordenesCompra->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $ordenesCompra->links() }}
    </div>
    @endif
</div>
@endsection
