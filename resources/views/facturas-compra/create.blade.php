@extends('layouts.app')

@section('title', 'Nueva Factura de Compra')
@section('page-title', 'Nueva Factura de Compra')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
        <a href="{{ route('facturas-compra.index') }}" class="hover:text-blue-600">Facturas de Compra</a>
        <i class="bi bi-chevron-right"></i>
        <span class="text-slate-800 font-medium">Nueva Factura</span>
    </div>
    <h3 class="text-2xl font-bold text-slate-800">Crear Nueva Factura de Compra</h3>
</div>

<form action="{{ route('facturas-compra.store') }}" method="POST" x-data="facturaCompraForm()">
    @csrf
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4">Información de la Factura</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    N° Factura <span class="text-red-500">*</span>
                </label>
                <input type="text" name="numero_factura" value="{{ old('numero_factura') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('numero_factura') border-red-500 @enderror">
                @error('numero_factura')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha') border-red-500 @enderror">
                @error('fecha')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Fecha Vencimiento <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_vencimiento') border-red-500 @enderror">
                @error('fecha_vencimiento')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Proveedor <span class="text-red-500">*</span>
                </label>
                <select name="proveedor_id" x-model="proveedorId" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor_id') border-red-500 @enderror">
                    <option value="">Seleccionar proveedor</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->razon_social }}
                        </option>
                    @endforeach
                </select>
                @error('proveedor_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Orden de Compra (Opcional)
                </label>
                <select name="orden_compra_id" x-model="ordenCompraId" @change="cargarOrdenCompra($event.target.value)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Sin orden de compra</option>
                    @foreach($ordenesCompra as $orden)
                        <option value="{{ $orden->id }}" {{ request('orden_compra_id') == $orden->id ? 'selected' : '' }}>
                            {{ $orden->numero }} - {{ $orden->proveedor->razon_social ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Observaciones
                </label>
                <textarea name="observaciones" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('observaciones') }}</textarea>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-semibold text-slate-800">Productos</h4>
            <button type="button" @click="agregarLinea()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
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
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(linea, index) in lineas" :key="index">
                        <tr>
                            <td class="px-4 py-3">
                                <input type="hidden" :name="'detalles['+index+'][producto_id]'" :value="linea.producto_id">
                                <div x-show="linea.producto_nombre">
                                    <p class="font-medium text-slate-800" x-text="linea.producto_nombre"></p>
                                    <p class="text-xs text-slate-500" x-text="linea.producto_codigo"></p>
                                </div>
                                <input x-show="!linea.producto_nombre" type="number" placeholder="ID del producto" :value="linea.producto_id" @input="linea.producto_id = $event.target.value" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" :name="'detalles['+index+'][cantidad]'" x-model="linea.cantidad" @input="calcularTotal(index)" step="0.01" min="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" :name="'detalles['+index+'][precio_unitario]'" x-model="linea.precio_unitario" @input="calcularTotal(index)" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold" x-text="'B/. ' + linea.total.toFixed(2)"></span>
                            </td>
                            <td class="px-4 py-3">
                                <button type="button" @click="eliminarLinea(index)" class="text-red-600 hover:text-red-900">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right font-semibold">Total General:</td>
                        <td class="px-4 py-3 text-xl font-bold text-blue-600" x-text="'B/. ' + totalGeneral.toFixed(2)"></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
            <i class="bi bi-save mr-2"></i>
            Guardar Factura
        </button>
        <a href="{{ route('facturas-compra.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition">
            <i class="bi bi-x-circle mr-2"></i>
            Cancelar
        </a>
    </div>
</form>

@push('scripts')
<script>
function facturaCompraForm() {
    return {
        lineas: [],
        ordenCompraId: '{{ request('orden_compra_id', '') }}',
        proveedorId: '',
        
        get totalGeneral() {
            return this.lineas.reduce((sum, linea) => sum + linea.total, 0);
        },
        
        agregarLinea() {
            this.lineas.push({
                producto_id: '',
                producto_nombre: '',
                producto_codigo: '',
                cantidad: 1,
                precio_unitario: 0,
                total: 0
            });
        },
        
        eliminarLinea(index) {
            this.lineas.splice(index, 1);
        },
        
        calcularTotal(index) {
            const linea = this.lineas[index];
            linea.total = (parseFloat(linea.cantidad) || 0) * (parseFloat(linea.precio_unitario) || 0);
        },
        
        async cargarOrdenCompra(ordenId) {
            if (!ordenId) {
                this.lineas = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/ordenes-compra/${ordenId}/detalles`);
                const data = await response.json();
                
                // Cargar proveedor
                this.proveedorId = data.orden.proveedor_id;
                
                // Cargar detalles de la orden con los precios correctos
                this.lineas = data.detalles.map(detalle => {
                    const cantidad = parseFloat(detalle.cantidad_solicitada) || 0;
                    const precio = parseFloat(detalle.precio_unitario) || 0;
                    return {
                        producto_id: detalle.producto_id,
                        producto_nombre: detalle.producto.nombre,
                        producto_codigo: detalle.producto.codigo,
                        cantidad: cantidad,
                        precio_unitario: precio,
                        total: cantidad * precio
                    };
                });
            } catch (error) {
                console.error('Error al cargar orden de compra:', error);
                alert('Error al cargar los datos de la orden de compra');
            }
        },
        
        init() {
            // Cargar orden de compra si viene en la URL
            if (this.ordenCompraId) {
                this.cargarOrdenCompra(this.ordenCompraId);
            }
        }
    }
}
</script>
@endpush
@endsection