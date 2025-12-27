@extends('layouts.app')

@section('title', 'Nueva Ruta Diaria')
@section('page-title', 'Nueva Ruta Diaria')

@section('content')
<div class="max-w-6xl mx-auto" x-data="rutaDiaria()">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('rutas-diarias.store') }}" @submit="validateForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Repartidor *</label>
                    <select name="repartidor_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar repartidor</option>
                        @foreach($repartidores as $repartidor)
                            <option value="{{ $repartidor->id }}">{{ $repartidor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bodega Móvil</label>
                    <select name="bodega_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sin bodega móvil</option>
                        @foreach($bodegas as $bodega)
                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }} - {{ $bodega->placa_vehiculo }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Ruta</label>
                    <select name="ruta_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sin ruta predefinida</option>
                        @foreach($rutas as $ruta)
                            <option value="{{ $ruta->id }}">{{ $ruta->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-semibold text-slate-800">Facturas a Entregar</h4>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">
                                    <input type="checkbox" @change="toggleAll" class="w-4 h-4 text-blue-600 rounded">
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facturasPendientes as $factura)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="facturas[]" value="{{ $factura->id }}" x-model="facturasSeleccionadas" class="w-4 h-4 text-blue-600 rounded">
                                </td>
                                <td class="px-4 py-3 font-mono">{{ $factura->numero }}</td>
                                <td class="px-4 py-3">{{ $factura->cliente->nombre_comercial }}</td>
                                <td class="px-4 py-3">B/. {{ number_format($factura->total, 2) }}</td>
                                <td class="px-4 py-3 font-bold text-red-600">B/. {{ number_format($factura->saldo_pendiente, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <p class="text-sm text-blue-800">
                        <span class="font-bold" x-text="facturasSeleccionadas.length"></span> facturas seleccionadas
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('rutas-diarias.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-check-circle mr-2"></i>Crear Ruta
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function rutaDiaria() {
    return {
        facturasSeleccionadas: [],
        
        toggleAll(e) {
            if (e.target.checked) {
                this.facturasSeleccionadas = Array.from(document.querySelectorAll('input[name="facturas[]"]')).map(cb => cb.value);
            } else {
                this.facturasSeleccionadas = [];
            }
        },
        
        validateForm(e) {
            if (this.facturasSeleccionadas.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos una factura');
            }
        }
    }
}
</script>
@endpush
@endsection
