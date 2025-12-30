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

<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="bi bi-exclamation-triangle text-yellow-400 text-xl"></i>
        </div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700">
                Esta funcionalidad está en desarrollo. Por favor, contacta al administrador del sistema.
            </p>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-6">
    <p class="text-slate-600">Para registrar una recepción de compra:</p>
    <ol class="list-decimal list-inside mt-4 space-y-2 text-slate-600">
        <li>Debe existir una orden de compra aprobada</li>
        <li>Los productos deben estar registrados en el sistema</li>
        <li>La bodega de destino debe estar activa</li>
    </ol>
</div>
@endsection
