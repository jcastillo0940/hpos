@extends('layouts.app')

@section('title', 'Nuevo Cliente')
@section('page-title', 'Nuevo Cliente')

@section('content')
<div class="max-w-7xl mx-auto" x-data="clienteForm()">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('clientes.store') }}" @submit="validateForm">
            @csrf
            
            <!-- Información Principal -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b">Información Principal</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Código *</label>
                        <input type="text" name="codigo" required value="{{ old('codigo') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('codigo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipo Identificación *</label>
                        <select name="tipo_identificacion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="ruc">RUC</option>
                            <option value="cedula">Cédula</option>
                            <option value="pasaporte">Pasaporte</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Identificación *</label>
                        <input type="text" name="identificacion" required value="{{ old('identificacion') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('identificacion')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Razón Social</label>
                        <input type="text" name="razon_social" value="{{ old('razon_social') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nombre Comercial *</label>
                        <input type="text" name="nombre_comercial" required value="{{ old('nombre_comercial') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('nombre_comercial')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Dirección Principal</label>
                        <textarea name="direccion" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('direccion') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Configuración Comercial -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-slate-800 mb-4 pb-2 border-b">Configuración Comercial</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Vendedor</label>
                        <select name="vendedor_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sin asignar</option>
                            @foreach($vendedores as $vendedor)
                                <option value="{{ $vendedor->id }}">{{ $vendedor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Zona</label>
                        <select name="zona_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sin zona</option>
                            @foreach($zonas as $zona)
                                <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Ruta</label>
                        <select name="ruta_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Sin ruta</option>
                            @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id }}">{{ $ruta->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <i class="bi bi-tag text-blue-600 mr-1"></i>Lista de Precios
                        </label>
                        <select name="lista_precio_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Lista estándar</option>
                            @foreach($listaPrecios as $lista)
                                <option value="{{ $lista->id }}">{{ $lista->nombre }} ({{ $lista->tipo_ajuste == 'porcentaje' ? $lista->valor_ajuste . '%' : 'B/. ' . number_format($lista->valor_ajuste, 2) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Límite de Crédito</label>
                        <input type="number" step="0.01" name="limite_credito" value="{{ old('limite_credito', 0) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Días de Crédito</label>
                        <input type="number" name="dias_credito" value="{{ old('dias_credito', 0) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <!-- Sucursales -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4 pb-2 border-b">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-building text-blue-600 mr-2"></i>Sucursales
                    </h3>
                    <button type="button" @click="agregarSucursal" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm">
                        <i class="bi bi-plus-circle mr-2"></i>Agregar Sucursal
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(sucursal, index) in sucursales" :key="index">
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-medium text-slate-700" x-text="'Sucursal ' + (index + 1)"></h4>
                                <button type="button" @click="eliminarSucursal(index)" class="text-red-600 hover:text-red-800">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Código *</label>
                                    <input type="text" :name="'sucursales['+index+'][codigo]'" required x-model="sucursal.codigo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Nombre *</label>
                                    <input type="text" :name="'sucursales['+index+'][nombre]'" required x-model="sucursal.nombre" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Dirección *</label>
                                    <input type="text" :name="'sucursales['+index+'][direccion]'" required x-model="sucursal.direccion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Teléfono</label>
                                    <input type="text" :name="'sucursales['+index+'][telefono]'" x-model="sucursal.telefono" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Zona</label>
                                    <select :name="'sucursales['+index+'][zona_id]'" x-model="sucursal.zona_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sin zona</option>
                                        @foreach($zonas as $zona)
                                            <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-2">Ruta</label>
                                    <select :name="'sucursales['+index+'][ruta_id]'" x-model="sucursal.ruta_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Sin ruta</option>
                                        @foreach($rutas as $ruta)
                                            <option value="{{ $ruta->id }}">{{ $ruta->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-3">
                                    <label class="block text-sm font-medium text-slate-700 mb-2">
                                        <i class="bi bi-tag text-blue-600 mr-1"></i>Lista de Precios Específica
                                    </label>
                                    <select :name="'sucursales['+index+'][lista_precio_id]'" x-model="sucursal.lista_precio_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Usar lista del cliente</option>
                                        @foreach($listaPrecios as $lista)
                                            <option value="{{ $lista->id }}">{{ $lista->nombre }} ({{ $lista->tipo_ajuste == 'porcentaje' ? $lista->valor_ajuste . '%' : 'B/. ' . number_format($lista->valor_ajuste, 2) }})</option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-slate-500 mt-1">Si no se selecciona, se usará la lista de precios del cliente</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="sucursales.length === 0" class="text-center py-8 text-slate-400">
                        <i class="bi bi-building text-4xl mb-2"></i>
                        <p>No hay sucursales agregadas</p>
                        <p class="text-sm">Haz clic en "Agregar Sucursal" para crear una</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('clientes.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">Cancelar</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm hover:shadow-md">
                    <i class="bi bi-check-circle mr-2"></i>Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function clienteForm() {
    return {
        sucursales: [],
        
        agregarSucursal() {
            this.sucursales.push({
                codigo: '',
                nombre: '',
                direccion: '',
                telefono: '',
                zona_id: '',
                ruta_id: '',
                lista_precio_id: ''
            });
        },
        
        eliminarSucursal(index) {
            if (confirm('¿Eliminar esta sucursal?')) {
                this.sucursales.splice(index, 1);
            }
        },
        
        validateForm(e) {
            // Validación adicional si es necesaria
        }
    }
}
</script>
@endpush
@endsection
