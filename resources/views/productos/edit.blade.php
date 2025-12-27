@extends('layouts.app')

@section('title', 'Editar Producto')
@section('page-title', 'Editar Producto')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Código *</label>
                    <input type="text" name="codigo" required value="{{ old('codigo', $producto->codigo) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Código de Barras</label>
                    <input type="text" name="codigo_barra" value="{{ old('codigo_barra', $producto->codigo_barra) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nombre *</label>
                    <input type="text" name="nombre" required value="{{ old('nombre', $producto->nombre) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Categoría</label>
                    <select name="categoria_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sin categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ $producto->categoria_id == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Imagen</label>
                    <input type="file" name="imagen" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" class="mt-2 w-20 h-20 object-cover rounded">
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Precio de Venta *</label>
                    <input type="number" step="0.01" name="precio_venta" required value="{{ old('precio_venta', $producto->precio_venta) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Costo Unitario *</label>
                    <input type="number" step="0.01" name="costo_unitario" required value="{{ old('costo_unitario', $producto->costo_unitario) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">ITBMS (%) *</label>
                    <input type="number" step="0.01" name="itbms" required value="{{ old('itbms', $producto->itbms) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Stock Mínimo</label>
                    <input type="number" step="0.01" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('productos.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">Actualizar Producto</button>
            </div>
        </form>
    </div>
</div>
@endsection
