@extends('layouts.app')

@section('title', 'Cobros')
@section('page-title', 'Cobros')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Cobros</h3>
        <p class="text-slate-600 mt-1">Registra y gestiona los cobros de clientes</p>
    </div>
    <a href="{{ route('cobros.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Registrar Cobro
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Total Cobros</p>
                <p class="text-2xl font-bold text-green-600">{{ $cobros->total() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-cash-coin text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Pendientes</p>
                <p class="text-2xl font-bold text-amber-600">{{ $cobros->where('estado', 'pendiente')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-clock text-amber-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Aplicados</p>
                <p class="text-2xl font-bold text-blue-600">{{ $cobros->where('estado', 'aplicado')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-check-circle text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Monto Total</p>
                <p class="text-2xl font-bold text-slate-800">B/. {{ number_format($cobros->sum('monto'), 2) }}</p>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo Pago</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referencia</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($cobros as $cobro)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono font-medium text-green-600">{{ $cobro->numero }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $cobro->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-slate-800">{{ $cobro->cliente->nombre_comercial }}</div>
                        <div class="text-xs text-slate-500">{{ $cobro->cliente->identificacion }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($cobro->tipo_pago === 'efectivo')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="bi bi-cash mr-1"></i> Efectivo
                        </span>
                        @elseif($cobro->tipo_pago === 'cheque')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="bi bi-file-earmark-check mr-1"></i> Cheque
                        </span>
                        @elseif($cobro->tipo_pago === 'transferencia')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="bi bi-bank mr-1"></i> Transferencia
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="bi bi-credit-card mr-1"></i> {{ ucfirst($cobro->tipo_pago) }}
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-slate-600 font-mono">{{ $cobro->referencia ?? '-' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-lg font-bold text-green-600">B/. {{ number_format($cobro->monto, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($cobro->estado === 'pendiente')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            <i class="bi bi-clock mr-1"></i> Pendiente
                        </span>
                        @elseif($cobro->estado === 'aplicado')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="bi bi-check-circle mr-1"></i> Aplicado
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="bi bi-x-circle mr-1"></i> Anulado
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('cobros.show', $cobro) }}" class="text-blue-600 hover:text-blue-900 transition" title="Ver detalles">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            @if($cobro->estado === 'pendiente')
                            <form method="POST" action="{{ route('cobros.aplicar', $cobro) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900 transition" title="Aplicar cobro" onclick="return confirm('¿Aplicar este cobro a las facturas?')">
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
                            <p class="text-lg font-medium">No hay cobros registrados</p>
                            <p class="text-sm">Registra tu primer cobro</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($cobros->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $cobros->links() }}
    </div>
    @endif
</div>
@endsection
