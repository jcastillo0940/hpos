@extends('layouts.app')

@section('title', 'Editar Bodega')
@section('page-title', 'Editar Bodega')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
        <a href="{{ route('bodegas.index') }}" class="hover:text-blue-600">Bodegas</a>
        <i class="bi bi-chevron-right"></i>
        <span class="text-slate-800 font-medium">Editar Bodega</span>
    </div>
    <h3 class="text-2xl font-bold text-slate-800">Editar Bodega: {{ $bodega->nombre }}</h3>
</div>

<form action="{{ route('bodegas.update', $bodega) }}" method="POST" class="max-w-4xl">
    @csrf
    @method('PUT')
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
            <i class="bi bi-info-circle mr-2 text-blue-600"></i>
            Información General
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Código <span class="text-red-500">*</span>
                </label>
                <input type="text" name="codigo" value="{{ old('codigo', $bodega->codigo) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo') border-red-500 @enderror">
                @error('codigo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Nombre <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nombre" value="{{ old('nombre', $bodega->nombre) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre') border-red-500 @enderror">
                @error('nombre')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Tipo <span class="text-red-500">*</span>
                </label>
                <select name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tipo') border-red-500 @enderror" x-data="{ tipo: '{{ old('tipo', $bodega->tipo) }}' }" x-model="tipo" @change="$dispatch('tipo-changed', tipo)">
                    <option value="Principal" {{ old('tipo', $bodega->tipo) == 'Principal' ? 'selected' : '' }}>Principal</option>
                    <option value="Secundaria" {{ old('tipo', $bodega->tipo) == 'Secundaria' ? 'selected' : '' }}>Secundaria</option>
                    <option value="Móvil" {{ old('tipo', $bodega->tipo) == 'Móvil' ? 'selected' : '' }}>Móvil</option>
                </select>
                @error('tipo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ showPlaca: '{{ old('tipo', $bodega->tipo) }}' === 'Móvil' }" @tipo-changed.window="showPlaca = $event.detail === 'Móvil'">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Placa del Vehículo
                    <span x-show="showPlaca" class="text-red-500">*</span>
                </label>
                <input type="text" name="placa_vehiculo" value="{{ old('placa_vehiculo', $bodega->placa_vehiculo) }}" :required="showPlaca" :disabled="!showPlaca" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('placa_vehiculo') border-red-500 @enderror" :class="!showPlaca && 'bg-gray-100'">
                @error('placa_vehiculo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Dirección
                </label>
                <textarea name="direccion" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion') border-red-500 @enderror">{{ old('direccion', $bodega->direccion) }}</textarea>
                @error('direccion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
            <i class="bi bi-person-badge mr-2 text-blue-600"></i>
            Responsable
        </h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Nombre del Responsable
                </label>
                <input type="text" name="responsable" value="{{ old('responsable', $bodega->responsable) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('responsable') border-red-500 @enderror">
                @error('responsable')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Teléfono
                </label>
                <input type="text" name="telefono" value="{{ old('telefono', $bodega->telefono) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('telefono') border-red-500 @enderror">
                @error('telefono')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div x-data="{ showRepartidor: '{{ old('tipo', $bodega->tipo) }}' === 'Móvil' }" @tipo-changed.window="showRepartidor = $event.detail === 'Móvil'">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Repartidor Asignado
                </label>
                <select name="repartidor_id" :disabled="!showRepartidor" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('repartidor_id') border-red-500 @enderror" :class="!showRepartidor && 'bg-gray-100'">
                    <option value="">Seleccionar repartidor</option>
                    @foreach($repartidores as $repartidor)
                        <option value="{{ $repartidor->id }}" {{ old('repartidor_id', $bodega->repartidor_id) == $repartidor->id ? 'selected' : '' }}>
                            {{ $repartidor->name }}
                        </option>
                    @endforeach
                </select>
                @error('repartidor_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4 flex items-center">
            <i class="bi bi-toggle-on mr-2 text-blue-600"></i>
            Estado
        </h4>
        
        <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="activa" value="1" {{ old('activa', $bodega->activa) ? 'checked' : '' }} class="sr-only peer">
                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                <span class="ml-3 text-sm font-medium text-slate-700">Bodega Activa</span>
            </label>
        </div>
    </div>

    <div class="flex items-center space-x-4">
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
            <i class="bi bi-save mr-2"></i>
            Actualizar Bodega
        </button>
        <a href="{{ route('bodegas.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition">
            <i class="bi bi-x-circle mr-2"></i>
            Cancelar
        </a>
    </div>
</form>
@endsection
