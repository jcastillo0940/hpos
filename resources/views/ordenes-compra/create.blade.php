@extends('layouts.app')

@section('title', 'Nueva Orden de Compra')
@section('page-title', 'Nueva Orden de Compra')

@section('content')
<div class="max-w-6xl mx-auto" x-data="ordenCompra()">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('ordenes-compra.store') }}" @submit="prepareSubmit">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Proveedor *</label>
                    <select name="proveedor_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar proveedor</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}">{{ $proveedor->razon_social }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bodega Destino *</label>
                    <select name="bodega_destino_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar bodega</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha Entrega Esperada</label>
                    <input type="date" name="fecha_entrega_esperada" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-semibold text-slate-800">Productos</h4>
                    <button type="button" @click="agregarProducto" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm">
                        <i class="bi bi-plus-circle mr-2"></i>Agregar Producto
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-2">
                                        <select :name="'detalles['+index+'][producto_id]'" required x-model="item.producto_id" @change="actualizarPrecio(index)" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Seleccionar</option>
                                            @foreach($productos as $producto)
                                                <option value="{{ $producto->id }}" data-costo="{{ $producto->costo_unitario }}">{{ $producto->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" :name="'detalles['+index+'][cantidad_solicitada]'" required step="0.01" x-model="item.cantidad" @input="calcularSubtotal(index)" class="w-24 px-3 py-2 border border-gray-300 rounded-lg">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" :name="'detalles['+index+'][precio_unitario]'" required step="0.01" x-model="item.precio_unitario" @input="calcularSubtotal(index)" class="w-32 px-3 py-2 border border-gray-300 rounded-lg">
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="font-bold" x-text="'B/. ' + item.subtotal.toFixed(2)"></span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <button type="button" @click="eliminarProducto(index)" class="text-red-600 hover:text-red-800">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="text-right">
                    <p class="text-lg font-semibold">Total: <span class="text-blue-600" x-text="'B/. ' + calcularTotal().toFixed(2)"></span></p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('ordenes-compra.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="bi bi-check-circle mr-2"></i>Guardar Orden
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function ordenCompra() {
    return {
        items: [],
        agregarProducto() {
            this.items.push({
                producto_id: '',
                cantidad: 1,
                precio_unitario: 0,
                subtotal: 0
            });
        },
        eliminarProducto(index) {
            this.items.splice(index, 1);
        },
        actualizarPrecio(index) {
            const select = event.target;
            const costo = select.options[select.selectedIndex].dataset.costo;
            this.items[index].precio_unitario = parseFloat(costo);
            this.calcularSubtotal(index);
        },
        calcularSubtotal(index) {
            const item = this.items[index];
            item.subtotal = item.cantidad * item.precio_unitario;
        },
        calcularTotal() {
            return this.items.reduce((total, item) => total + item.subtotal, 0);
        },
        prepareSubmit(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto');
            }
        }
    }
}
</script>
@endpush
@endsection
