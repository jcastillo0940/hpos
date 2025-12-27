@extends('layouts.app')

@section('title', 'Productos')
@section('page-title', 'Productos')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Catálogo de Productos</h3>
        <p class="text-slate-600 mt-1">Gestiona tu inventario de productos</p>
    </div>
    <a href="{{ route('productos.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nuevo Producto
    </a>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" placeholder="Buscar por código, nombre o código de barras..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <select name="categoria_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $categoria)
                    <option value="{{ $categoria->id }}" {{ request('categoria_id') == $categoria->id ? 'selected' : '' }}>
                        {{ $categoria->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition">
            <i class="bi bi-search mr-2"></i>Buscar
        </button>
    </form>
</div>

<!-- Products Grid/Table Toggle -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden" x-data="{ view: 'table' }">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <p class="text-sm text-slate-600">{{ $productos->total() }} productos encontrados</p>
        <div class="flex items-center space-x-2">
            <button @click="view = 'table'" :class="view === 'table' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-slate-600'" class="px-3 py-1.5 rounded-lg transition">
                <i class="bi bi-list-ul"></i>
            </button>
            <button @click="view = 'grid'" :class="view === 'grid' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-slate-600'" class="px-3 py-1.5 rounded-lg transition">
                <i class="bi bi-grid-3x3-gap"></i>
            </button>
        </div>
    </div>

    <!-- Table View -->
    <div x-show="view === 'table'" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($productos as $producto)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($producto->imagen)
                                <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-12 h-12 rounded-lg object-cover" alt="{{ $producto->nombre }}">
                            @else
                                <div class="w-12 h-12 bg-slate-200 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-box text-slate-400 text-xl"></i>
                                </div>
                            @endif
                            <div class="ml-3">
                                <p class="font-medium text-slate-800">{{ $producto->nombre }}</p>
                                <p class="text-sm text-slate-500">{{ $producto->codigo_barra ?? 'Sin código de barras' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-sm font-medium text-slate-700">{{ $producto->codigo }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                        {{ $producto->categoria->nombre ?? 'Sin categoría' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-medium text-slate-800">B/. {{ number_format($producto->precio_venta, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button onclick="verStock({{ $producto->id }})" class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition text-sm font-medium">
                            <i class="bi bi-box-seam mr-1"></i>
                            Ver Stock
                        </button>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($producto->activo)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Activo
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Inactivo
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end space-x-2">
                            <a href="{{ route('productos.show', $producto) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalles">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                            <a href="{{ route('productos.edit', $producto) }}" class="text-amber-600 hover:text-amber-900" title="Editar">
                                <i class="bi bi-pencil text-lg"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-inbox text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay productos registrados</p>
                            <p class="text-sm">Comienza agregando tu primer producto</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Grid View -->
    <div x-show="view === 'grid'" class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($productos as $producto)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden hover:shadow-lg transition group">
                <div class="aspect-square bg-gray-100 overflow-hidden">
                    @if($producto->imagen)
                        <img src="{{ asset('storage/' . $producto->imagen) }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300" alt="{{ $producto->nombre }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="bi bi-box text-slate-300 text-6xl"></i>
                        </div>
                    @endif
                </div>
                <div class="p-4">
                    <p class="text-xs text-slate-500 mb-1">{{ $producto->codigo }}</p>
                    <h4 class="font-semibold text-slate-800 mb-2 line-clamp-2">{{ $producto->nombre }}</h4>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-lg font-bold text-blue-600">B/. {{ number_format($producto->precio_venta, 2) }}</span>
                        @if($producto->activo)
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Activo</span>
                        @endif
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('productos.show', $producto) }}" class="flex-1 text-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition">
                            Ver
                        </a>
                        <a href="{{ route('productos.edit', $producto) }}" class="flex-1 text-center px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm rounded-lg transition">
                            Editar
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-12 text-center text-slate-400">
                <i class="bi bi-inbox text-5xl mb-3"></i>
                <p class="text-lg font-medium">No hay productos</p>
            </div>
            @endforelse
        </div>
    </div>

    @if($productos->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        {{ $productos->links() }}
    </div>
    @endif
</div>

<!-- Modal Stock -->
<div x-data="{ open: false, stockData: null }" @open-stock.window="open = true; stockData = $event.detail">
    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div @click="open = false" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>
            
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-800">Stock por Bodega</h3>
                    <button @click="open = false" class="text-slate-400 hover:text-slate-600">
                        <i class="bi bi-x-lg text-xl"></i>
                    </button>
                </div>
                
                <div id="stockContent" class="space-y-3">
                    <div class="text-center py-8">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verStock(productoId) {
    window.dispatchEvent(new CustomEvent('open-stock'));
    
    fetch(`/productos/${productoId}/stock`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800">Stock Total Disponible</p>
                    <p class="text-2xl font-bold text-blue-900">${data.stock_total}</p>
                </div>
                <div class="space-y-2">
            `;
            
            data.stocks.forEach(stock => {
                html += `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-slate-800">${stock.bodega.nombre}</p>
                            <p class="text-xs text-slate-500">${stock.bodega.tipo}</p>
                        </div>
                        <span class="text-lg font-bold text-slate-800">${stock.cantidad_disponible}</span>
                    </div>
                `;
            });
            
            html += '</div>';
            document.getElementById('stockContent').innerHTML = html;
        });
}
</script>
@endpush
@endsection