@extends('layouts.app')

@section('title', 'Nueva Recepción de Compra')
@section('page-title', 'Nueva Recepción de Compra')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
        <a href="{{ route('recepciones-compra.index') }}" class="hover:text-blue-600">Recepciones de Compra</a>
        <i class="bi bi-chevron-right"></i>
        <span class="text-slate-800 font-medium">Nueva Recepción</span>
    </div>
    <h3 class="text-2xl font-bold text-slate-800">Registrar Recepción de Compra</h3>
</div>

<form action="{{ route('recepciones-compra.store') }}" method="POST" x-data="recepcionCompraForm()">
    @csrf
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4">Información de la Recepción</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Orden de Compra <span class="text-red-500">*</span>
                </label>
                <select name="orden_compra_id" x-model="ordenCompraId" @change="cargarOrdenCompra()" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('orden_compra_id') border-red-500 @enderror">
                    <option value="">Seleccionar orden de compra</option>
                    @foreach($ordenesCompra as $orden)
                        <option value="{{ $orden->id }}" data-proveedor="{{ $orden->proveedor->razon_social ?? 'N/A' }}" data-bodega="{{ $orden->bodega_destino_id }}">
                            {{ $orden->numero }} - {{ $orden->proveedor->razon_social ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                @error('orden_compra_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Bodega Destino <span class="text-red-500">*</span>
                </label>
                <select name="bodega_id" x-model="bodegaId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bodega_id') border-red-500 @enderror">
                    <option value="">Seleccionar bodega</option>
                    @foreach($bodegas as $bodega)
                        <option value="{{ $bodega->id }}">{{ $bodega->nombre }}</option>
                    @endforeach
                </select>
                @error('bodega_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Fecha de Recepción <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha') border-red-500 @enderror">
                @error('fecha')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Observaciones
                </label>
                <textarea name="observaciones" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('observaciones') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Información del Proveedor -->
    <div x-show="proveedorNombre" class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <div class="flex items-center">
            <i class="bi bi-building text-blue-600 text-2xl mr-3"></i>
            <div>
                <p class="text-sm text-blue-600 font-medium">Proveedor</p>
                <p class="text-lg font-bold text-blue-900" x-text="proveedorNombre"></p>
            </div>
        </div>
    </div>

    <!-- Productos a Recibir -->
    <div x-show="productos.length > 0" class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-semibold text-slate-800">Productos a Recibir</h4>
            <button type="button" @click="recibirTodo()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                <i class="bi bi-check-all mr-2"></i>Recibir Todo
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Solicitado</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ya Recibido</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Pendiente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Recibir Ahora</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(producto, index) in productos" :key="index">
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3">
                                <input type="hidden" :name="'detalles['+index+'][producto_id]'" :value="producto.producto_id">
                                <div>
                                    <p class="font-medium text-slate-800" x-text="producto.nombre"></p>
                                    <p class="text-xs text-slate-500" x-text="producto.codigo"></p>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-700" x-text="producto.cantidad_solicitada"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-blue-600" x-text="producto.cantidad_recibida || 0"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold" :class="producto.pendiente > 0 ? 'text-orange-600' : 'text-green-600'" x-text="producto.pendiente"></span>
                            </td>
                            <td class="px-4 py-3">
                                <input 
                                    type="number" 
                                    :name="'detalles['+index+'][cantidad_recibida]'" 
                                    x-model="producto.recibir_ahora" 
                                    :max="producto.pendiente"
                                    min="0"
                                    step="0.01"
                                    class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="producto.recibir_ahora > producto.pendiente ? 'border-red-500' : ''"
                                >
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right font-semibold text-slate-700">Total a Recibir:</td>
                        <td class="px-4 py-3">
                            <span class="text-xl font-bold text-green-600" x-text="totalRecibir"></span>
                            <span class="text-sm text-slate-600"> unidades</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Mensaje cuando no hay orden seleccionada -->
    <div x-show="!ordenCompraId" class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-12 text-center">
        <i class="bi bi-inbox text-6xl text-gray-400 mb-4"></i>
        <p class="text-lg font-medium text-gray-600 mb-2">Selecciona una Orden de Compra</p>
        <p class="text-sm text-gray-500">Elige una orden de compra aprobada para comenzar el registro de recepción</p>
    </div>

    <!-- Botones de Acción -->
    <div x-show="productos.length > 0" class="flex items-center space-x-4">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
            <i class="bi bi-save mr-2"></i>
            Registrar Recepción
        </button>
        <a href="{{ route('recepciones-compra.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition">
            <i class="bi bi-x-circle mr-2"></i>
            Cancelar
        </a>
    </div>
</form>

@push('scripts')
<script>
function recepcionCompraForm() {
    return {
        ordenCompraId: '{{ old('orden_compra_id', '') }}',
        bodegaId: '{{ old('bodega_id', '') }}',
        proveedorNombre: '',
        productos: [],
        
        get totalRecibir() {
            return this.productos.reduce((sum, p) => sum + (parseFloat(p.recibir_ahora) || 0), 0);
        },
        
        async cargarOrdenCompra() {
            if (!this.ordenCompraId) {
                this.productos = [];
                this.proveedorNombre = '';
                return;
            }
            
            try {
                const response = await fetch(`/api/ordenes-compra/${this.ordenCompraId}/detalles`);
                const data = await response.json();
                
                // Cargar información del proveedor
                const select = document.querySelector('select[name="orden_compra_id"]');
                const selectedOption = select.options[select.selectedIndex];
                this.proveedorNombre = selectedOption.dataset.proveedor;
                
                // Auto-seleccionar bodega si la orden tiene una
                if (selectedOption.dataset.bodega) {
                    this.bodegaId = selectedOption.dataset.bodega;
                }
                
                // Cargar productos
                this.productos = data.detalles.map(detalle => ({
                    producto_id: detalle.producto_id,
                    nombre: detalle.producto.nombre,
                    codigo: detalle.producto.codigo,
                    cantidad_solicitada: parseFloat(detalle.cantidad_solicitada),
                    cantidad_recibida: parseFloat(detalle.cantidad_recibida || 0),
                    pendiente: parseFloat(detalle.cantidad_solicitada) - parseFloat(detalle.cantidad_recibida || 0),
                    recibir_ahora: 0
                }));
                
            } catch (error) {
                console.error('Error al cargar orden de compra:', error);
                alert('Error al cargar los detalles de la orden de compra');
            }
        },
        
        recibirTodo() {
            this.productos.forEach(producto => {
                producto.recibir_ahora = producto.pendiente;
            });
        },
        
        init() {
            if (this.ordenCompraId) {
                this.cargarOrdenCompra();
            }
        }
    }
}
</script>
@endpush
@endsection
