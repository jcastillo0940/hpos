@extends('layouts.app')

@section('title', 'Nueva Nota de Crédito')
@section('page-title', 'Nueva Nota de Crédito')

@section('content')
<div class="max-w-6xl mx-auto" x-data="notaCredito()">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('notas-credito.store') }}" @submit="prepareSubmit">
            @csrf
            
            <input type="hidden" name="factura_id" value="{{ $factura->id ?? '' }}">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Factura</label>
                    <input type="text" value="{{ $factura->numero ?? '' }}" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo *</label>
                    <select name="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="devolucion">Devolución (Reingresa a Stock)</option>
                        <option value="merma">Merma/Vencido (No Reingresa)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Motivo *</label>
                    <select name="motivo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="producto_dañado">Producto Dañado</option>
                        <option value="producto_vencido">Producto Vencido</option>
                        <option value="error_facturacion">Error de Facturación</option>
                        <option value="devolucion_comercial">Devolución Comercial</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Observaciones</label>
                    <textarea name="observaciones" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
            </div>

            <div class="mb-6">
                <h4 class="font-semibold text-slate-800 mb-4">Productos a Devolver</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad Original</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad a Devolver</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($factura))
                                @foreach($factura->detalles as $index => $detalle)
                                <tr>
                                    <td class="px-4 py-3">{{ $detalle->producto->nombre }}</td>
                                    <td class="px-4 py-3">{{ $detalle->cantidad }}</td>
                                    <td class="px-4 py-3">
                                        <input type="hidden" name="detalles[{{ $index }}][factura_detalle_id]" value="{{ $detalle->id }}">
                                        <input type="number" name="detalles[{{ $index }}][cantidad]" step="0.01" min="0" max="{{ $detalle->cantidad }}" value="0" class="w-24 px-3 py-2 border border-gray-300 rounded-lg">
                                    </td>
                                    <td class="px-4 py-3">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td class="px-4 py-3 font-bold">B/. {{ number_format($detalle->total, 2) }}</td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('notas-credito.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-check-circle mr-2"></i>Guardar Nota de Crédito
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function notaCredito() {
    return {
        prepareSubmit(e) {
            const cantidades = document.querySelectorAll('input[name*="[cantidad]"]');
            let tieneDevolucion = false;
            cantidades.forEach(input => {
                if (parseFloat(input.value) > 0) tieneDevolucion = true;
            });
            
            if (!tieneDevolucion) {
                e.preventDefault();
                alert('Debe devolver al menos un producto');
            }
        }
    }
}
</script>
@endpush
@endsection
