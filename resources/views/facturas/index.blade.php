@extends('layouts.app')

@section('title', 'Facturas')
@section('page-title', 'Facturas')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Facturas de Venta</h3>
        <p class="text-slate-600 mt-1">Gestiona las facturas emitidas</p>
    </div>
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
                <option value="pagada" {{ request('estado') == 'pagada' ? 'selected' : '' }}>Pagada</option>
                <option value="parcial" {{ request('estado') == 'parcial' ? 'selected' : '' }}>Pago Parcial</option>
                <option value="vencida" {{ request('estado') == 'vencida' ? 'selected' : '' }}>Vencida</option>
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
                <p class="text-sm text-slate-600">Total Facturado</p>
                <p class="text-2xl font-bold text-slate-800">B/. {{ number_format($facturas->sum('total'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-cash-stack text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Por Cobrar</p>
                <p class="text-2xl font-bold text-amber-600">B/. {{ number_format($facturas->whereIn('estado', ['pendiente', 'parcial', 'vencida'])->sum('saldo_pendiente'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-hourglass-split text-amber-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Cobrado</p>
                <p class="text-2xl font-bold text-green-600">B/. {{ number_format($facturas->where('estado', 'pagada')->sum('total'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Vencidas</p>
                <p class="text-2xl font-bold text-red-600">B/. {{ number_format($facturas->where('estado', 'vencida')->sum('saldo_pendiente'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-exclamation-triangle text-red-600 text-xl"></i>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Factura</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($facturas as $factura)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-bold text-blue-600">{{ $factura->numero }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $factura->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <p class="font-medium text-slate-800">{{ $factura->cliente->nombre_comercial }}</p>
                            <p class="text-sm text-slate-500">{{ $factura->cliente->identificacion }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $factura->vendedor->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-bold text-slate-800">B/. {{ number_format($factura->total, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-semibold {{ $factura->saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600' }}">
                            B/. {{ number_format($factura->saldo_pendiente, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @switch($factura->estado)
                            @case('pendiente')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    Pendiente
                                </span>
                                @break
                            @case('pagada')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Pagada
                                </span>
                                @break
                            @case('parcial')