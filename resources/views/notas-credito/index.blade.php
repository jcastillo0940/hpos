@extends('layouts.app')

@section('title', 'Notas de Crédito')
@section('page-title', 'Notas de Crédito')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Notas de Crédito</h3>
        <p class="text-slate-600 mt-1">Gestiona las notas de crédito de facturas</p>
    </div>
    <a href="{{ route('notas-credito.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nueva Nota de Crédito
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Total Notas</p>
                <p class="text-2xl font-bold text-blue-600">{{ $notasCredito->total() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-arrow-return-left text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Pendientes</p>
                <p class="text-2xl font-bold text-amber-600">{{ $notasCredito->where('estado', 'pendiente')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-clock text-amber-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Aplicadas</p>
                <p class="text-2xl font-bold text-green-600">{{ $notasCredito->where('estado', 'aplicada')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Monto Total</p>
                <p class="text-2xl font-bold text-slate-800">B/. {{ number_format($notasCredito->sum('total'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-cash-stack text-slate-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($notasCredito as $nota)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-medium text-blue-600">{{ $nota->numero }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $nota->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-slate-800">{{ $nota->cliente->nombre_comercial }}</div>
                        <div class="text-xs text-slate-500">{{ $nota->cliente->identificacion }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($nota->factura)
                        <a href="{{ route('facturas.show', $nota->factura) }}" class="text-blue-600 hover:text-blue-800 font-mono text-sm">
                            {{ $nota->factura->numero }}
                        </a>
                        @else
                        <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($nota->tipo === 'devolucion')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="bi bi-arrow-counterclockwise mr-1"></i> Devolución
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="bi bi-dash-circle mr-1"></i> Descuento
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-lg font-bold text-slate-800">B/. {{ number_format($nota->total, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($nota->estado === 'pendiente')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <i class="bi bi-clock mr-1"></i> Pendiente
                        </span>
                        @elseif($nota->estado === 'aplicada')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="bi bi-check-circle mr-1"></i> Aplicada
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="bi bi-x-circle mr-1"></i> Anulada
                        </span>
                        @endif
                    </td>
                   <td class="px-6 py-4 whitespace-nowrap text-right">
    <div class="flex items-center justify-end space-x-2">
        <a href="{{ route('notas-credito.show', $nota) }}" class="text-blue-600 hover:text-blue-900 transition" title="Ver detalles">
            <i class="bi bi-eye text-lg"></i>
        </a>
        <a href="{{ route('notas-credito.pdf', $nota) }}" target="_blank" class="text-red-600 hover:text-red-900 transition" title="PDF">
            <i class="bi bi-file-pdf text-lg"></i>
        </a>
        @if($nota->estado === 'pendiente')
        <form method="POST" action="{{ route('notas-credito.aplicar', $nota) }}" class="inline">
            @csrf
            <button type="submit" class="text-green-600 hover:text-green-900 transition" title="Aplicar" onclick="return confirm('¿Aplicar esta nota de crédito?')">
                <i class="bi bi-check-circle text-lg"></i>
            </button>
        </form>
        @endif
    </div>
</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay notas de crédito</p>
                            <p class="text-sm">Crea tu primera nota de crédito</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($notasCredito->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $notasCredito->links() }}
    </div>
    @endif
</div>
@endsection
