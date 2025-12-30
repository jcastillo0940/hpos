@extends('layouts.app')

@section('title', 'Detalle del Proveedor')
@section('page-title', 'Detalle del Proveedor')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('proveedores.index') }}" class="text-slate-600 hover:text-slate-800">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-slate-800">{{ $proveedor->nombre_comercial }}</h2>
                    <p class="text-slate-600 mt-1">{{ $proveedor->codigo }} - {{ $proveedor->ruc }}{{ $proveedor->dv ? '-' . $proveedor->dv : '' }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('proveedores.edit', $proveedor) }}" 
                   class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition">
                    <i class="bi bi-pencil mr-2"></i>Editar
                </a>
                <form action="{{ route('proveedores.destroy', $proveedor) }}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('¿Estás seguro de eliminar este proveedor?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <i class="bi bi-trash mr-2"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información Básica -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-building mr-2"></i>Información Básica
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Código</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $proveedor->codigo }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">RUC</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $proveedor->ruc }}{{ $proveedor->dv ? '-' . $proveedor->dv : '' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Razón Social</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $proveedor->razon_social }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Nombre Comercial</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $proveedor->nombre_comercial }}</dd>
                        </div>
                        @if($proveedor->direccion)
                        <div class="md:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Dirección</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $proveedor->direccion }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Información de Contacto -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-telephone mr-2"></i>Información de Contacto
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if($proveedor->email)
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Email</dt>
                            <dd class="mt-1 text-sm text-slate-900">
                                <a href="mailto:{{ $proveedor->email }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $proveedor->email }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        @if($proveedor->telefono)
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Teléfono</dt>
                            <dd class="mt-1 text-sm text-slate-900">
                                <a href="tel:{{ $proveedor->telefono }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $proveedor->telefono }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        @if($proveedor->contacto_nombre)
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Persona de Contacto</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $proveedor->contacto_nombre }}</dd>
                        </div>
                        @endif
                        @if($proveedor->contacto_telefono)
                        <div>
                            <dt class="text-sm font-medium text-slate-500">Teléfono de Contacto</dt>
                            <dd class="mt-1 text-sm text-slate-900">
                                <a href="tel:{{ $proveedor->contacto_telefono }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $proveedor->contacto_telefono }}
                                </a>
                            </dd>
                        </div>
                        @endif
                        @if(!$proveedor->email && !$proveedor->telefono && !$proveedor->contacto_nombre && !$proveedor->contacto_telefono)
                        <div class="md:col-span-2 text-center py-4 text-slate-500">
                            No hay información de contacto registrada
                        </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Observaciones -->
            @if($proveedor->observaciones)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-chat-text mr-2"></i>Observaciones
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-slate-700 whitespace-pre-line">{{ $proveedor->observaciones }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Lateral -->
        <div class="space-y-6">
            <!-- Resumen Financiero -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-cash-stack mr-2"></i>Resumen Financiero
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm font-medium text-slate-500">Saldo por Pagar</p>
                        <p class="text-2xl font-bold {{ $proveedor->saldo_actual > 0 ? 'text-red-600' : 'text-green-600' }}">
                            B/. {{ number_format($proveedor->saldo_actual, 2) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-500">Condición de Pago</p>
                        <p class="text-lg font-semibold text-slate-800">
                            @if($proveedor->dias_credito == 0)
                            Contado
                            @else
                            {{ $proveedor->dias_credito }} días
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-info-circle mr-2"></i>Estado
                    </h3>
                </div>
                <div class="p-6">
                    @if($proveedor->activo)
                    <div class="flex items-center space-x-2 text-green-600">
                        <i class="bi bi-check-circle text-2xl"></i>
                        <span class="font-semibold">Activo</span>
                    </div>
                    @else
                    <div class="flex items-center space-x-2 text-gray-600">
                        <i class="bi bi-x-circle text-2xl"></i>
                        <span class="font-semibold">Inactivo</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Auditoría -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-clock-history mr-2"></i>Auditoría
                    </h3>
                </div>
                <div class="p-6 space-y-3 text-sm">
                    <div>
                        <p class="text-slate-500">Creado</p>
                        <p class="text-slate-900">{{ $proveedor->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500">Última Modificación</p>
                        <p class="text-slate-900">{{ $proveedor->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b bg-slate-50">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-lightning mr-2"></i>Acciones Rápidas
                    </h3>
                </div>
                <div class="p-6 space-y-2">
                    <a href="#" class="block w-full px-4 py-2 text-center bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition">
                        <i class="bi bi-file-earmark-plus mr-2"></i>Nueva Orden de Compra
                    </a>
                    <a href="#" class="block w-full px-4 py-2 text-center bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition">
                        <i class="bi bi-cash mr-2"></i>Registrar Pago
                    </a>
                    <a href="#" class="block w-full px-4 py-2 text-center bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition">
                        <i class="bi bi-file-text mr-2"></i>Ver Estado de Cuenta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
