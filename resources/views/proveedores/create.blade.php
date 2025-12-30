@extends('layouts.app')

@section('title', 'Crear Proveedor')
@section('page-title', 'Crear Proveedor')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <a href="{{ route('proveedores.index') }}" class="text-slate-600 hover:text-slate-800">
                <i class="bi bi-arrow-left text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800">Nuevo Proveedor</h2>
                <p class="text-slate-600 mt-1">Registra un nuevo proveedor en el sistema</p>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <form action="{{ route('proveedores.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Información Básica -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    <i class="bi bi-building mr-2"></i>Información Básica
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Código -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Código <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('codigo') border-red-500 @enderror">
                        @error('codigo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- RUC -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            RUC <span class="text-red-500">*</span>
                        </label>
                        <div class="flex space-x-2">
                            <input type="text" name="ruc" value="{{ old('ruc') }}" required
                                   placeholder="1234567890"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('ruc') border-red-500 @enderror">
                            <input type="text" name="dv" value="{{ old('dv') }}" maxlength="2"
                                   placeholder="DV"
                                   class="w-16 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('dv') border-red-500 @enderror">
                        </div>
                        @error('ruc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Razón Social -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Razón Social <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="razon_social" value="{{ old('razon_social') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('razon_social') border-red-500 @enderror">
                        @error('razon_social')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre Comercial -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            Nombre Comercial <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nombre_comercial" value="{{ old('nombre_comercial') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('nombre_comercial') border-red-500 @enderror">
                        @error('nombre_comercial')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    <i class="bi bi-telephone mr-2"></i>Información de Contacto
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('telefono') border-red-500 @enderror">
                        @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Dirección</label>
                        <textarea name="direccion" rows="2"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('direccion') border-red-500 @enderror">{{ old('direccion') }}</textarea>
                        @error('direccion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Persona de Contacto -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Persona de Contacto</label>
                        <input type="text" name="contacto_nombre" value="{{ old('contacto_nombre') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('contacto_nombre') border-red-500 @enderror">
                        @error('contacto_nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono de Contacto -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Teléfono de Contacto</label>
                        <input type="text" name="contacto_telefono" value="{{ old('contacto_telefono') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('contacto_telefono') border-red-500 @enderror">
                        @error('contacto_telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Condiciones Comerciales -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    <i class="bi bi-cash-coin mr-2"></i>Condiciones Comerciales
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Días de Crédito -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Días de Crédito</label>
                        <input type="number" name="dias_credito" value="{{ old('dias_credito', 0) }}" min="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('dias_credito') border-red-500 @enderror">
                        <p class="mt-1 text-xs text-slate-500">0 = Contado</p>
                        @error('dias_credito')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Estado</label>
                        <select name="activo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="1" {{ old('activo', 1) == 1 ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ old('activo') == 0 ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('activo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Observaciones -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Observaciones</label>
                        <textarea name="observaciones" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('observaciones') border-red-500 @enderror">{{ old('observaciones') }}</textarea>
                        @error('observaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="p-6 bg-slate-50 flex justify-end space-x-3">
                <a href="{{ route('proveedores.index') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-slate-700 hover:bg-slate-100 transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-save mr-2"></i>Guardar Proveedor
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
