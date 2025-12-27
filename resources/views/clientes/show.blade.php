@extends('layouts.app')

@section('title', 'Detalle Cliente')
@section('page-title', 'Detalle Cliente')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $cliente->nombre_comercial }}</h3>
                <p class="text-slate-600">{{ $cliente->codigo }} - {{ $cliente->identificacion }}</p>
                @if($cliente->razon_social && $cliente->razon_social != $cliente->nombre_comercial)
                    <p class="text-sm text-slate-500">{{ $cliente->razon_social }}</p>
                @endif
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('clientes.edit', $cliente) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition">
                    <i class="bi bi-pencil mr-2"></i>Editar
                </a>
                <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" onsubmit="return confirm('¿Estás seguro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="bi bi-trash mr-2"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Email</p>
                <p class="font-medium">{{ $cliente->email ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Teléfono</p>
                <p class="font-medium">{{ $cliente->telefono ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Vendedor</p>
                <p class="font-medium">{{ $cliente->vendedor->name ?? 'Sin asignar' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Zona / Ruta</p>
                <p class="font-medium">{{ $cliente->zona->nombre ?? 'N/A' }} / {{ $cliente->ruta->nombre ?? 'N/A' }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 p-4 bg-blue-50 rounded-lg">
            <div>
                <p class="text-sm text-blue-700">Lista de Precios</p>
                <p class="font-bold text-blue-900">{{ $cliente->listaPrecio->nombre ?? 'Estándar' }}</p>
            </div>
            <div>
                <p class="text-sm text-green-700">Límite Crédito</p>
                <p class="font-bold text-green-900">B/. {{ number_format($cliente->limite_credito, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-red-700">Saldo Actual</p>
                <p class="font-bold text-red-900">B/. {{ number_format($cliente->saldo_actual, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-700">Días Crédito</p>
                <p class="font-bold text-slate-900">{{ $cliente->dias_credito }} días</p>
            </div>
        </div>

        @if($cliente->direccion)
        <div class="mb-6">
            <p class="text-sm text-slate-600 mb-1">Dirección Principal</p>
            <p class="font-medium">{{ $cliente->direccion }}</p>
        </div>
        @endif
    </div>

    <!-- Sucursales -->
    @if($cliente->sucursales->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="font-semibold text-slate-800 mb-4 flex items-center">
            <i class="bi bi-building text-blue-600 mr-2 text-xl"></i>
            Sucursales ({{ $cliente->sucursales->count() }})
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($cliente->sucursales as $sucursal)
            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition">
                <div class="flex justify-between items-start mb-2">
                    <h5 class="font-semibold text-slate-800">{{ $sucursal->nombre }}</h5>
                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">{{ $sucursal->codigo }}</span>
                </div>
                <p class="text-sm text-slate-600 mb-2">{{ $sucursal->direccion }}</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    @if($sucursal->telefono)
                    <div>
                        <p class="text-slate-500">Teléfono:</p>
                        <p class="font-medium">{{ $sucursal->telefono }}</p>
                    </div>
                    @endif
                    @if($sucursal->zona)
                    <div>
                        <p class="text-slate-500">Zona:</p>
                        <p class="font-medium">{{ $sucursal->zona->nombre }}</p>
                    </div>
                    @endif
                    @if($sucursal->ruta)
                    <div>
                        <p class="text-slate-500">Ruta:</p>
                        <p class="font-medium">{{ $sucursal->ruta->nombre }}</p>
                    </div>
                    @endif
                    @if($sucursal->listaPrecio)
                    <div>
                        <p class="text-slate-500"><i class="bi bi-tag text-blue-600"></i> Lista Precios:</p>
                        <p class="font-medium text-blue-700">{{ $sucursal->listaPrecio->nombre }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Últimas Facturas -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h4 class="font-semibold text-slate-800 mb-4">Últimas Facturas</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($facturas as $factura)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <a href="{{ route('facturas.show', $factura) }}" class="text-blue-600 hover:underline">
                                {{ $factura->numero }}
                            </a>
                        </td>
                        <td class="px-4 py-2">{{ $factura->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 font-bold">B/. {{ number_format($factura->total, 2) }}</td>
                        <td class="px-4 py-2 font-bold {{ $factura->saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600' }}">
                            B/. {{ number_format($factura->saldo_pendiente, 2) }}
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @switch($factura->estado)
                                    @case('pagada') bg-green-100 text-green-800 @break
                                    @case('pendiente') bg-amber-100 text-amber-800 @break
                                    @case('parcial') bg-blue-100 text-blue-800 @break
                                    @case('vencida') bg-red-100 text-red-800 @break
                                @endswitch">
                                {{ ucfirst($factura->estado) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-slate-400">No hay facturas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
