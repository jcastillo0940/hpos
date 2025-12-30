@extends('layouts.app')

@section('title', 'Lista de Precios - ' . $lista->nombre)
@section('page-title', 'Lista de Precios')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $lista->nombre }}</h3>
                <p class="text-slate-600">{{ $lista->codigo }}</p>
                @if($lista->descripcion)
                <p class="text-sm text-slate-500 mt-1">{{ $lista->descripcion }}</p>
                @endif
            </div>
            <div class="flex space-x-2">
                @if($lista->es_default)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                    <i class="bi bi-star-fill mr-1"></i> Por defecto
                </span>
                @endif
                
                @if($lista->activa)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="bi bi-check-circle mr-1"></i> Activa
                </span>
                @else
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                    <i class="bi bi-x-circle mr-1"></i> Inactiva
                </span>
                @endif
            </div>
        </div>
        
        <div class="flex justify-between items-center">
            <div class="flex space-x-4">
                <div>
                    <p class="text-sm text-slate-600">Total Productos</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $lista->productosDetalle->count() }}</p>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <button onclick="document.getElementById('modalAplicarGlobal').classList.remove('hidden')" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                    <i class="bi bi-lightning-fill mr-2"></i>Aplicar Global
                </button>
                
                <form method="POST" action="{{ route('listas-precios.recalcular', $lista) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition">
                        <i class="bi bi-arrow-clockwise mr-2"></i>Recalcular Precios
                    </button>
                </form>
                
                <a href="{{ route('listas-precios.edit', $lista) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-pencil mr-2"></i>Editar Lista
                </a>
                
                <a href="{{ route('listas-precios.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                    <i class="bi bi-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>
    </div>
    
    <!-- Agregar Producto -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6" x-data="{ mostrarFormulario: false }">
        <button @click="mostrarFormulario = !mostrarFormulario" class="w-full flex items-center justify-between px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition">
            <span class="font-medium text-blue-700">
                <i class="bi bi-plus-circle mr-2"></i>Agregar Producto a la Lista
            </span>
            <i class="bi" :class="mostrarFormulario ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
        </button>
        
        <form x-show="mostrarFormulario" x-cloak method="POST" action="{{ route('listas-precios.agregar-producto', $lista) }}" class="mt-4" x-data="precioProducto()">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Producto *</label>
                    <select name="producto_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" @change="seleccionarProducto($event)">
                        <option value="">Seleccionar producto...</option>
                        @foreach($productos as $producto)
                        <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}">
                            {{ $producto->codigo }} - {{ $producto->nombre }} (Precio base: B/. {{ number_format($producto->precio_venta, 2) }})
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Precio *</label>
                    <select name="tipo_precio" x-model="tipoPrecio" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="fijo">Precio Fijo</option>
                        <option value="porcentaje">Porcentaje</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <span x-show="tipoPrecio === 'fijo'">Precio Fijo (B/.)</span>
                        <span x-show="tipoPrecio === 'porcentaje'">Porcentaje (%)</span>
                    </label>
                    
                    <input x-show="tipoPrecio === 'fijo'" type="number" name="precio" step="0.01" min="0" x-model="precio" @input="calcularPrecioFinal()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    
                    <input x-show="tipoPrecio === 'porcentaje'" type="number" name="porcentaje" step="0.01" x-model="porcentaje" @input="calcularPrecioFinal()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="ej: 10 o -5">
                </div>
            </div>
            
            <div class="mt-4 p-4 bg-blue-50 rounded-lg" x-show="precioCalculado > 0">
                <p class="text-sm font-medium text-blue-900">
                    <i class="bi bi-info-circle mr-1"></i>
                    Precio calculado: <span class="text-lg font-bold">B/. <span x-text="precioCalculado.toFixed(2)"></span></span>
                    <span x-show="tipoPrecio === 'porcentaje' && porcentaje != 0" class="ml-2 text-xs">
                        (<span x-text="porcentaje > 0 ? '+' + porcentaje : porcentaje"></span>% sobre precio base)
                    </span>
                </p>
            </div>
            
            <div class="mt-4 flex justify-end space-x-2">
                <button type="button" @click="mostrarFormulario = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-plus-circle mr-2"></i>Agregar
                </button>
            </div>
        </form>
    </div>
    
    <!-- Tabla de Productos -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <input type="text" id="searchProductos" placeholder="Buscar productos..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Base</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ajuste</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Final</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="productosTable">
                    @forelse($lista->productosDetalle as $detalle)
                    <tr class="hover:bg-gray-50 transition producto-row" data-nombre="{{ strtolower($detalle->producto->nombre) }}" data-codigo="{{ strtolower($detalle->producto->codigo) }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm text-slate-600">{{ $detalle->producto->codigo }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">{{ $detalle->producto->nombre }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-slate-600">B/. {{ number_format($detalle->producto->precio_venta, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($detalle->tipo_precio === 'fijo')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="bi bi-currency-dollar mr-1"></i> Fijo
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <i class="bi bi-percent mr-1"></i> Porcentaje
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($detalle->tipo_precio === 'fijo')
                            <span class="text-slate-600">B/. {{ number_format($detalle->precio, 2) }}</span>
                            @else
                            <span class="text-slate-600 font-medium">
                                {{ $detalle->porcentaje > 0 ? '+' : '' }}{{ number_format($detalle->porcentaje, 2) }}%
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-lg font-bold text-blue-600">B/. {{ number_format($detalle->precio_calculado, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button onclick="editarProducto({{ $detalle->producto->id }}, '{{ $detalle->tipo_precio }}', {{ $detalle->precio ?? 0 }}, {{ $detalle->porcentaje ?? 0 }}, {{ $detalle->producto->precio_venta }})" class="text-blue-600 hover:text-blue-900 transition" title="Editar">
                                    <i class="bi bi-pencil text-lg"></i>
                                </button>
                                
                                <form method="POST" action="{{ route('listas-precios.eliminar-producto', [$lista, $detalle->producto]) }}" class="inline" onsubmit="return confirm('¿Eliminar este producto de la lista?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Eliminar">
                                        <i class="bi bi-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <i class="bi bi-inbox text-5xl mb-3"></i>
                                <p class="text-lg font-medium">No hay productos en esta lista</p>
                                <p class="text-sm">Agrega productos para comenzar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Editar Producto -->
<div id="modalEditarProducto" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4" x-data="editarProductoModal()">
        <div class="p-6">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Editar Precio de Producto</h3>
            
            <form method="POST" :action="'/listas-precios/{{ $lista->id }}/productos/' + productoId" @submit="submitForm">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Precio</label>
                    <select name="tipo_precio" x-model="tipoPrecio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="fijo">Precio Fijo</option>
                        <option value="porcentaje">Porcentaje</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <span x-show="tipoPrecio === 'fijo'">Precio Fijo (B/.)</span>
                        <span x-show="tipoPrecio === 'porcentaje'">Porcentaje (%)</span>
                    </label>
                    
                    <input x-show="tipoPrecio === 'fijo'" type="number" name="precio" step="0.01" min="0" x-model="precio" @input="calcularPrecioFinal()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    
                    <input x-show="tipoPrecio === 'porcentaje'" type="number" name="porcentaje" step="0.01" x-model="porcentaje" @input="calcularPrecioFinal()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-blue-900">
                        Precio calculado: <span class="text-lg font-bold">B/. <span x-text="precioCalculado.toFixed(2)"></span></span>
                    </p>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="cerrarModal()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Aplicar Global -->
<div id="modalAplicarGlobal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Aplicar Ajuste Global</h3>
            <p class="text-sm text-slate-600 mb-4">Este ajuste se aplicará a TODOS los productos de la empresa</p>
            
            <form method="POST" action="{{ route('listas-precios.aplicar-global', $lista) }}">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Ajuste</label>
                    <select name="tipo_precio" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="porcentaje">Porcentaje sobre precio base</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Porcentaje (%)</label>
                    <input type="number" name="porcentaje" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="ej: 10 (aumento) o -5 (descuento)">
                    <p class="mt-1 text-xs text-slate-500">Ejemplo: +10 = aumenta 10%, -5 = descuenta 5%</p>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('modalAplicarGlobal').classList.add('hidden')" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                        Aplicar a Todos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Buscar productos
document.getElementById('searchProductos').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.producto-row').forEach(row => {
        const nombre = row.dataset.nombre;
        const codigo = row.dataset.codigo;
        if (nombre.includes(search) || codigo.includes(search)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Alpine.js component para agregar producto
function precioProducto() {
    return {
        tipoPrecio: 'fijo',
        precio: 0,
        porcentaje: 0,
        precioBase: 0,
        precioCalculado: 0,
        
        seleccionarProducto(event) {
            const option = event.target.selectedOptions[0];
            this.precioBase = parseFloat(option.dataset.precio || 0);
            this.calcularPrecioFinal();
        },
        
        calcularPrecioFinal() {
            if (this.tipoPrecio === 'fijo') {
                this.precioCalculado = parseFloat(this.precio || 0);
            } else {
                this.precioCalculado = this.precioBase + (this.precioBase * (parseFloat(this.porcentaje || 0) / 100));
            }
        }
    }
}

// Modal editar producto
function editarProductoModal() {
    return {
        productoId: 0,
        tipoPrecio: 'fijo',
        precio: 0,
        porcentaje: 0,
        precioBase: 0,
        precioCalculado: 0,
        
        calcularPrecioFinal() {
            if (this.tipoPrecio === 'fijo') {
                this.precioCalculado = parseFloat(this.precio || 0);
            } else {
                this.precioCalculado = this.precioBase + (this.precioBase * (parseFloat(this.porcentaje || 0) / 100));
            }
        },
        
        cerrarModal() {
            document.getElementById('modalEditarProducto').classList.add('hidden');
        },
        
        submitForm(event) {
            // El formulario se enviará normalmente
        }
    }
}

function editarProducto(id, tipo, precio, porcentaje, precioBase) {
    const modal = document.getElementById('modalEditarProducto');
    const alpineData = Alpine.$data(modal.querySelector('[x-data]'));
    
    alpineData.productoId = id;
    alpineData.tipoPrecio = tipo;
    alpineData.precio = precio;
    alpineData.porcentaje = porcentaje;
    alpineData.precioBase = precioBase;
    alpineData.calcularPrecioFinal();
    
    modal.classList.remove('hidden');
}
</script>
@endpush
@endsection
