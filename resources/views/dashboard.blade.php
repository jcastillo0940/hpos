@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Ventas Hoy -->
    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-cash-stack text-blue-600 text-2xl"></i>
            </div>
            <span class="text-xs font-medium text-green-600 bg-green-100 px-2 py-1 rounded-full">Hoy</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1">B/. {{ number_format($ventasHoy, 2) }}</h3>
        <p class="text-sm text-slate-600">Ventas de Hoy</p>
    </div>

    <!-- Ventas del Mes -->
    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-graph-up-arrow text-green-600 text-2xl"></i>
            </div>
            <span class="text-xs font-medium text-blue-600 bg-blue-100 px-2 py-1 rounded-full">Mes</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1">B/. {{ number_format($ventasMes, 2) }}</h3>
        <p class="text-sm text-slate-600">Ventas del Mes</p>
    </div>

    <!-- CxC Vencidas -->
    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded-full">Urgente</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1">B/. {{ number_format($cxcVencidas, 2) }}</h3>
        <p class="text-sm text-slate-600">Cuentas Vencidas</p>
    </div>

    <!-- Órdenes Pendientes -->
    <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-hourglass-split text-amber-600 text-2xl"></i>
            </div>
            <span class="text-xs font-medium text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Pendiente</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-1">{{ $ordenesPendientes }}</h3>
        <p class="text-sm text-slate-600">Órdenes Pendientes</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Alertas -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-slate-800">Alertas y Notificaciones</h3>
        </div>
        <div class="p-6 space-y-4">
            @if($productosStockBajo > 0)
                <div class="flex items-start space-x-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <i class="bi bi-exclamation-triangle text-amber-600 text-xl mt-0.5"></i>
                    <div>
                        <p class="font-medium text-amber-900">Stock Bajo</p>
                        <p class="text-sm text-amber-700">{{ $productosStockBajo }} productos están por debajo del stock mínimo</p>
                    </div>
                </div>
            @endif

            @if($cxcVencidas > 0)
                <div class="flex items-start space-x-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <i class="bi bi-exclamation-circle text-red-600 text-xl mt-0.5"></i>
                    <div>
                        <p class="font-medium text-red-900">Cuentas Vencidas</p>
                        <p class="text-sm text-red-700">B/. {{ number_format($cxcVencidas, 2) }} en cuentas por cobrar vencidas</p>
                    </div>
                </div>
            @endif

            @if($ordenesPendientes > 0)
                <div class="flex items-start space-x-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <i class="bi bi-info-circle text-blue-600 text-xl mt-0.5"></i>
                    <div>
                        <p class="font-medium text-blue-900">Órdenes Pendientes</p>
                        <p class="text-sm text-blue-700">{{ $ordenesPendientes }} órdenes de entrega esperando aprobación</p>
                    </div>
                </div>
            @endif

            @if($productosStockBajo == 0 && $cxcVencidas == 0 && $ordenesPendientes == 0)
                <div class="flex items-center justify-center py-8 text-slate-400">
                    <div class="text-center">
                        <i class="bi bi-check-circle text-4xl mb-2"></i>
                        <p>No hay alertas pendientes</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="bg-white rounded-xl shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-slate-800">Accesos Rápidos</h3>
        </div>
        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
            @can('crear_ordenes_entrega')
            <a href="{{ route('ordenes-entrega.create') }}" class="flex items-center space-x-3 p-4 border-2 border-blue-200 rounded-lg hover:bg-blue-50 hover:border-blue-400 transition group">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition">
                    <i class="bi bi-plus-circle text-blue-600 text-xl group-hover:text-white"></i>
                </div>
                <span class="font-medium text-slate-800">Nueva Orden</span>
            </a>
            @endcan

            @can('crear_facturas')
            <a href="{{ route('facturas.index') }}" class="flex items-center space-x-3 p-4 border-2 border-green-200 rounded-lg hover:bg-green-50 hover:border-green-400 transition group">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition">
                    <i class="bi bi-receipt text-green-600 text-xl group-hover:text-white"></i>
                </div>
                <span class="font-medium text-slate-800">Ver Facturas</span>
            </a>
            @endcan

            @can('registrar_cobros')
            <a href="{{ route('cobros.create') }}" class="flex items-center space-x-3 p-4 border-2 border-purple-200 rounded-lg hover:bg-purple-50 hover:border-purple-400 transition group">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-600 transition">
                    <i class="bi bi-cash-coin text-purple-600 text-xl group-hover:text-white"></i>
                </div>
                <span class="font-medium text-slate-800">Registrar Cobro</span>
            </a>
            @endcan

            @can('ver_reportes_ventas')
            <a href="{{ route('reportes.ventas') }}" class="flex items-center space-x-3 p-4 border-2 border-slate-200 rounded-lg hover:bg-slate-50 hover:border-slate-400 transition group">
                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center group-hover:bg-slate-600 transition">
                    <i class="bi bi-graph-up text-slate-600 text-xl group-hover:text-white"></i>
                </div>
                <span class="font-medium text-slate-800">Ver Reportes</span>
            </a>
            @endcan
        </div>
    </div>
</div>
@endsection