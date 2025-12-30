@extends('layouts.app')

@section('title', 'Detalle de Pago')
@section('page-title', 'Detalle de Pago')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
            <a href="{{ route('pagos.index') }}" class="hover:text-blue-600">Pagos</a>
            <i class="bi bi-chevron-right"></i>
            <span class="text-slate-800 font-medium">{{ $pago->numero }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $pago->numero }}</h3>
                <p class="text-slate-600">{{ $pago->fecha ? $pago->fecha->format('d/m/Y') : 'Sin fecha' }}</p>
            </div>
            <div class="flex items-center space-x-2">
                @if($pago->estado == 'aplicado')
                    <form action="{{ route('pagos.anular', $pago) }}" method="POST" onsubmit="return confirm('¿Estás seguro de anular este pago? Esta acción revertirá todos los cambios.')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                            <i class="bi bi-x-circle mr-2"></i>Anular Pago
                        </button>
                    </form>
                @endif
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($pago->estado == 'aplicado') bg-green-100 text-green-800
                    @elseif($pago->estado == 'pendiente') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif">
                    @if($pago->estado == 'aplicado')
                        <i class="bi bi-check-circle mr-1"></i>
                    @endif
                    {{ ucfirst($pago->estado) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Proveedor</p>
                <p class="font-medium text-slate-800">{{ $pago->proveedor->razon_social ?? 'N/A' }}</p>
                <p class="text-xs text-slate-500">RUC: {{ $pago->proveedor->ruc ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Tipo de Pago</p>
                <p class="font-medium text-slate-800">{{ ucfirst($pago->tipo_pago) }}</p>
                @if($pago->referencia)
                    <p class="text-xs text-slate-500">Ref: {{ $pago->referencia }}</p>
                @endif
                @if($pago->banco)
                    <p class="text-xs text-slate-500">Banco: {{ $pago->banco }}</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-slate-600">Monto Total</p>
                <p class="text-2xl font-bold text-blue-600">B/. {{ number_format($pago->monto, 2) }}</p>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6 mb-6">
            <h4 class="text-lg font-semibold text-slate-800 mb-4">Facturas Aplicadas</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">N° Factura</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Factura</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto Aplicado</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pago->detalles as $detalle)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm font-medium">{{ $detalle->facturaCompra->numero_factura ?? 'N/A' }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm">
                                {{ $detalle->facturaCompra->fecha ? $detalle->facturaCompra->fecha->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium">B/. {{ number_format($detalle->facturaCompra->total ?? 0, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-green-600">B/. {{ number_format($detalle->monto_aplicado, 2) }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('facturas-compra.show', $detalle->factura_compra_id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="bi bi-eye mr-1"></i>Ver Factura
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                No hay facturas aplicadas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-semibold text-slate-700">Total Aplicado:</td>
                            <td colspan="2" class="px-4 py-3">
                                <span class="text-xl font-bold text-green-600">B/. {{ number_format($pago->detalles->sum('monto_aplicado'), 2) }}</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        @if($pago->observaciones)
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm font-medium text-slate-700 mb-2">Observaciones:</p>
            <p class="text-sm text-slate-600">{{ $pago->observaciones }}</p>
        </div>
        @endif

        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-slate-600">Registrado por:</p>
                    <p class="font-medium text-slate-800">{{ $pago->usuario->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-slate-600">Fecha de registro:</p>
                    <p class="font-medium text-slate-800">{{ $pago->created_at ? $pago->created_at->format('d/m/Y H:i') : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
