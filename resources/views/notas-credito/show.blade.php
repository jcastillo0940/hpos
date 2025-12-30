@extends('layouts.app')

@section('title', 'Nota de Crédito - ' . $notaCredito->numero)
@section('page-title', 'Nota de Crédito')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $notaCredito->numero }}</h3>
                <p class="text-slate-600">{{ $notaCredito->fecha->format('d/m/Y') }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('notas-credito.pdf', $notaCredito) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                    <i class="bi bi-file-pdf mr-2"></i>PDF
                </a>
                <a href="{{ route('notas-credito.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                    <i class="bi bi-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-semibold text-slate-700 mb-3">Información del Cliente</h4>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium text-slate-600">Cliente:</span> {{ $notaCredito->cliente->nombre_comercial }}</p>
                    <p><span class="font-medium text-slate-600">RUC:</span> {{ $notaCredito->cliente->identificacion }}</p>
                    @if($notaCredito->factura)
                    <p><span class="font-medium text-slate-600">Factura:</span> 
                        <a href="{{ route('facturas.show', $notaCredito->factura) }}" class="text-blue-600 hover:underline">
                            {{ $notaCredito->factura->numero }}
                        </a>
                    </p>
                    @endif
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-slate-700 mb-3">Detalles de la Nota</h4>
                <div class="space-y-2 text-sm">
                    <p><span class="font-medium text-slate-600">Tipo:</span> 
                        @if($notaCredito->tipo === 'devolucion')
                        <span class="text-purple-600">Devolución</span>
                        @else
                        <span class="text-blue-600">Descuento/Ajuste</span>
                        @endif
                    </p>
                    <p><span class="font-medium text-slate-600">Motivo:</span> {{ $notaCredito->motivo }}</p>
                    <p><span class="font-medium text-slate-600">Estado:</span> 
                        @if($notaCredito->estado === 'pendiente')
                        <span class="text-amber-600">Pendiente</span>
                        @elseif($notaCredito->estado === 'aplicada')
                        <span class="text-green-600">Aplicada</span>
                        @else
                        <span class="text-red-600">Anulada</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    @if($notaCredito->detalles->count() > 0)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200">
            <h4 class="font-semibold text-slate-800">Productos</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ITBMS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($notaCredito->detalles as $detalle)
                    <tr>
                        <td class="px-6 py-4">{{ $detalle->producto->nombre }}</td>
                        <td class="px-6 py-4">{{ $detalle->cantidad }}</td>
                        <td class="px-6 py-4">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="px-6 py-4">B/. {{ number_format($detalle->subtotal, 2) }}</td>
                        <td class="px-6 py-4">B/. {{ number_format($detalle->itbms_monto, 2) }}</td>
                        <td class="px-6 py-4 font-bold">B/. {{ number_format($detalle->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-end">
            <div class="w-80 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="font-medium text-slate-600">Subtotal:</span>
                    <span class="font-bold">B/. {{ number_format($notaCredito->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="font-medium text-slate-600">ITBMS:</span>
                    <span class="font-bold">B/. {{ number_format($notaCredito->itbms, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg border-t-2 pt-2">
                    <span class="font-bold text-slate-800">TOTAL:</span>
                    <span class="font-bold text-blue-600">B/. {{ number_format($notaCredito->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
