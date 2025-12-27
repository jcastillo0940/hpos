@extends('layouts.app')

@section('title', 'Detalle Factura')
@section('page-title', 'Factura')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $factura->numero }}</h3>
                <p class="text-slate-600">{{ $factura->fecha->format('d/m/Y') }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('facturas.pdf', $factura) }}" target="_blank" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <i class="bi bi-file-pdf mr-2"></i>Descargar PDF
                </a>
                
                @if($factura->saldo_pendiente >= $factura->total && $factura->estado != 'anulada')
                <form method="POST" action="{{ route('facturas.anular', $factura) }}" onsubmit="return confirm('¿Anular esta factura?')">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="bi bi-x-circle mr-2"></i>Anular
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Cliente</p>
                <p class="font-medium">{{ $factura->cliente->nombre_comercial }}</p>
                <p class="text-sm text-slate-500">{{ $factura->cliente->identificacion }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Vendedor</p>
                <p class="font-medium">{{ $factura->vendedor->name }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Estado</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    @switch($factura->estado)
                        @case('pendiente') bg-amber-100 text-amber-800 @break
                        @case('pagada') bg-green-100 text-green-800 @break
                        @case('parcial') bg-blue-100 text-blue-800 @break
                        @case('vencida') bg-red-100 text-red-800 @break
                        @case('anulada') bg-gray-100 text-gray-800 @break
                    @endswitch">
                    {{ ucfirst($factura->estado) }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto mb-6">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ITBMS</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($factura->detalles as $detalle)
                    <tr>
                        <td class="px-4 py-3">{{ $detalle->producto->nombre }}</td>
                        <td class="px-4 py-3">{{ $detalle->cantidad }}</td>
                        <td class="px-4 py-3">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="px-4 py-3">B/. {{ number_format($detalle->subtotal, 2) }}</td>
                        <td class="px-4 py-3">B/. {{ number_format($detalle->itbms_monto, 2) }}</td>
                        <td class="px-4 py-3 font-bold">B/. {{ number_format($detalle->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold">Subtotal:</td>
                        <td class="px-4 py-3 font-bold">B/. {{ number_format($factura->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold">ITBMS:</td>
                        <td class="px-4 py-3 font-bold">B/. {{ number_format($factura->itbms, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold text-lg">Total:</td>
                        <td class="px-4 py-3 text-xl font-bold text-blue-600">B/. {{ number_format($factura->total, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold">Saldo Pendiente:</td>
                        <td class="px-4 py-3 font-bold text-red-600">B/. {{ number_format($factura->saldo_pendiente, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($factura->observaciones)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm font-medium text-blue-900">Observaciones:</p>
            <p class="text-blue-800">{{ $factura->observaciones }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
