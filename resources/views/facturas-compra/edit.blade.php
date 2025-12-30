@extends('layouts.app')

@section('title', 'Editar Factura de Compra')
@section('page-title', 'Editar Factura de Compra')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
        <a href="{{ route('facturas-compra.index') }}" class="hover:text-blue-600">Facturas de Compra</a>
        <i class="bi bi-chevron-right"></i>
        <span class="text-slate-800 font-medium">Editar Factura</span>
    </div>
    <h3 class="text-2xl font-bold text-slate-800">Editar Factura: {{ $facturaCompra->numero_factura }}</h3>
</div>

<form action="{{ route('facturas-compra.update', $facturaCompra) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4">Información de la Factura</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    N° Factura <span class="text-red-500">*</span>
                </label>
                <input type="text" name="numero_factura" value="{{ old('numero_factura', $facturaCompra->numero_factura) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('numero_factura') border-red-500 @enderror">
                @error('numero_factura')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha" value="{{ old('fecha', $facturaCompra->fecha?->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha') border-red-500 @enderror">
                @error('fecha')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Fecha Vencimiento <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento', $facturaCompra->fecha_vencimiento?->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_vencimiento') border-red-500 @enderror">
                @error('fecha_vencimiento')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Observaciones
                </label>
                <textarea name="observaciones" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('observaciones', $facturaCompra->observaciones) }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4">Resumen</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-slate-600">Proveedor</p>
                <p class="font-medium">{{ $facturaCompra->proveedor->razon_social ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Total</p>
                <p class="text-2xl font-bold text-blue-600">B/. {{ number_format($facturaCompra->total, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-slate-600">Saldo Pendiente</p>
                <p class="text-2xl font-bold text-red-600">B/. {{ number_format($facturaCompra->saldo_pendiente, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
            <i class="bi bi-save mr-2"></i>
            Actualizar Factura
        </button>
        <a href="{{ route('facturas-compra.show', $facturaCompra) }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition">
            <i class="bi bi-x-circle mr-2"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
