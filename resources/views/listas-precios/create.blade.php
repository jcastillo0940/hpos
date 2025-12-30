@extends('layouts.app')

@section('title', 'Crear Lista de Precios')
@section('page-title', 'Crear Lista de Precios')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('listas-precios.store') }}">
            @csrf
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Información General</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Código *</label>
                        <input type="text" name="codigo" value="{{ old('codigo') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo') border-red-500 @enderror">
                        @error('codigo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Nombre *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre') border-red-500 @enderror">
                        @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Descripción</label>
                    <textarea name="descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>
                    @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-4 flex items-center space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="es_default" value="1" {{ old('es_default') ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-slate-700">Lista por defecto</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="activa" value="1" {{ old('activa', true) ? 'checked' : '' }} class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm text-slate-700">Activa</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <a href="{{ route('listas-precios.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm hover:shadow-md">
                    <i class="bi bi-save mr-2"></i>Crear Lista
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
