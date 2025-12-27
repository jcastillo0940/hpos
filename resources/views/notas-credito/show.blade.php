@extends('layouts.app')

@section('title', 'Detalle Nota de Crédito')
@section('page-title', 'Nota de Crédito')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $notaCredito->numero }}</h3>
                <p class="text-slate-600">{{ $notaCredito->fecha->format('d/m/Y') }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                {{ $notaCredito->tipo == 'devolucion' ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800' }}">
                {{ $notaCredito->tipo == 'devolucion' ? 'Devolución' : 'Merma/Vencido' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Factura Relacionada</p>
                <a href="{{ route('facturas.show', $notaCredito->factura) }}" class="font-medium text-blue-600 hover:underline">{{ $notaCredito->factura->numero }}</a>
            </div>
            <div>
                <p class="text-sm text-slate-600">Cliente</p>
                <p class="font-medium">{{ $notaCredito->cliente->nombre_comercial }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Motivo</p>
                <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $notaCredito->motivo)) }}</p>
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
                    @foreach($notaCredito->detalles as $detalle)
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
                        <td class="px-4 py-3 font-bold">B/. {{ number_format($notaCredito->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold">ITBMS:</td>
                        <td class="px-4 py-3 font-bold">B/. {{ number_format($notaCredito->itbms, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold text-lg">Total:</td>
                        <td class="px-4 py-3 text-xl font-bold text-red-600">B/. {{ number_format($notaCredito->total, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        @if($notaCredito->observaciones)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm font-medium text-blue-900">Observaciones:</p>
            <p class="text-blue-800">{{ $notaCredito->observaciones }}</p>
        </div>
        @endif

        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
            <p class="text-sm font-medium text-amber-900">
                <i class="bi bi-info-circle mr-2"></i>
                @if($notaCredito->tipo == 'devolucion')
                    Los productos fueron reingresados al inventario.
                @else
                    Los productos NO fueron reingresados al inventario (pérdida por merma/vencimiento).
                @endif
            </p>
        </div>
    </div>
</div>
@endsection
