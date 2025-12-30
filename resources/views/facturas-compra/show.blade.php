@extends('layouts.app')

@section('title', 'Detalle Factura de Compra')
@section('page-title', 'Factura de Compra')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
            <a href="{{ route('facturas-compra.index') }}" class="hover:text-blue-600">Facturas de Compra</a>
            <i class="bi bi-chevron-right"></i>
            <span class="text-slate-800 font-medium">{{ $facturaCompra->numero_factura }}</span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $facturaCompra->numero_factura }}</h3>
                <p class="text-slate-600">{{ $facturaCompra->fecha ? $facturaCompra->fecha->format('d/m/Y') : 'Sin fecha' }}</p>
            </div>
            <div class="flex items-center space-x-2">
                @if($facturaCompra->estado == 'pendiente')
                    <a href="{{ route('facturas-compra.edit', $facturaCompra) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition">
                        <i class="bi bi-pencil mr-2"></i>Editar
                    </a>
                @endif
                
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                    @if($facturaCompra->estado == 'pendiente') bg-yellow-100 text-yellow-800
                    @elseif($facturaCompra->estado == 'pagada') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($facturaCompra->estado) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-slate-600">Proveedor</p>
                <p class="font-medium">{{ $facturaCompra->proveedor->razon_social ?? 'N/A' }}</p>
                <p class="text-xs text-slate-500">RUC: {{ $facturaCompra->proveedor->ruc ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Orden de Compra</p>
                @if($facturaCompra->ordenCompra)
                    <a href="{{ route('ordenes-compra.show', $facturaCompra->ordenCompra) }}" class="font-medium text-blue-600 hover:text-blue-800">
                        {{ $facturaCompra->ordenCompra->numero }}
                    </a>
                @else
                    <p class="font-medium text-slate-400">Sin orden de compra</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-slate-600">Vencimiento</p>
                <p class="font-medium">{{ $facturaCompra->fecha_vencimiento ? $facturaCompra->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</p>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-6 mb-6">
            <div class="overflow-x-auto">
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
                        @foreach($facturaCompra->detalles as $detalle)
                        <tr>
                            <td class="px-4 py-3">{{ $detalle->producto->nombre ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $detalle->cantidad }}</td>
                            <td class="px-4 py-3">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="px-4 py-3">B/. {{ number_format($detalle->subtotal, 2) }}</td>
                            <td class="px-4 py-3">B/. {{ number_format($detalle->itbms_monto, 2) }}</td>
                            <td class="px-4 py-3 font-bold">B/. {{ number_format($detalle->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2">
                @if($facturaCompra->observaciones)
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm font-medium text-slate-700 mb-2">Observaciones:</p>
                    <p class="text-sm text-slate-600">{{ $facturaCompra->observaciones }}</p>
                </div>
                @endif
            </div>
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-slate-600">Subtotal:</span>
                    <span class="font-medium">B/. {{ number_format($facturaCompra->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-slate-600">ITBMS:</span>
                    <span class="font-medium">B/. {{ number_format($facturaCompra->itbms, 2) }}</span>
                </div>
                <div class="flex justify-between mb-2 pt-2 border-t border-blue-200">
                    <span class="font-semibold text-slate-800">Total:</span>
                    <span class="text-xl font-bold text-blue-600">B/. {{ number_format($facturaCompra->total, 2) }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-blue-200">
                    <span class="font-semibold text-slate-800">Saldo Pendiente:</span>
                    <span class="text-xl font-bold {{ $facturaCompra->saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600' }}">
                        B/. {{ number_format($facturaCompra->saldo_pendiente, 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if($facturaCompra->pagos->count() > 0)
    <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4">Historial de Pagos</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">N° Pago</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($facturaCompra->pagos as $pago)
                    <tr>
                        <td class="px-4 py-3">{{ $pago->pago->fecha->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $pago->pago->numero }}</td>
                        <td class="px-4 py-3">{{ $pago->pago->metodo_pago }}</td>
                        <td class="px-4 py-3 font-bold text-green-600">B/. {{ number_format($pago->monto, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
