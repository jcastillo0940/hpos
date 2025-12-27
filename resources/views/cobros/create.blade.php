@extends('layouts.app')

@section('title', 'Registrar Cobro')
@section('page-title', 'Nuevo Cobro')

@section('content')
<div class="max-w-6xl mx-auto" x-data="cobroForm()">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('cobros.store') }}" @submit="validateForm">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cliente *</label>
                    <select name="cliente_id" x-model="clienteId" @change="cargarFacturas" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>{{ $cliente->nombre_comercial }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Pago *</label>
                    <select name="tipo_pago" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="efectivo">Efectivo</option>
                        <option value="cheque">Cheque</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="ach">ACH</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Referencia</label>
                    <input type="text" name="referencia" placeholder="Número de cheque/transferencia" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Banco</label>
                    <input type="text" name="banco" placeholder="Nombre del banco" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Monto Total *</label>
                    <input type="number" step="0.01" name="monto" x-model="montoTotal" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Observaciones</label>
                    <textarea name="observaciones" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                </div>
            </div>

            <div class="mb-6" x-show="facturasPendientes.length > 0">
                <h4 class="font-semibold text-slate-800 mb-4">Aplicar a Facturas</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Factura</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Saldo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto a Aplicar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="facturasPendientes.length === 0">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                        {{ isset($cliente) ? 'El cliente no tiene facturas pendientes' : 'Seleccione un cliente' }}
                                    </td>
                                </tr>
                            </template>
                            <template x-for="(factura, index) in facturasPendientes" :key="factura.id">
                                <tr>
                                    <td class="px-4 py-3" x-text="factura.numero"></td>
                                    <td class="px-4 py-3" x-text="factura.fecha"></td>
                                    <td class="px-4 py-3" x-text="'B/. ' + parseFloat(factura.total).toFixed(2)"></td>
                                    <td class="px-4 py-3 font-bold text-red-600" x-text="'B/. ' + parseFloat(factura.saldo_pendiente).toFixed(2)"></td>
                                    <td class="px-4 py-3">
                                        <input type="hidden" :name="'facturas['+index+'][factura_id]'" :value="factura.id">
                                        <input type="number" step="0.01" :name="'facturas['+index+'][monto_aplicado]'" x-model="factura.monto_aplicado" :max="factura.saldo_pendiente" @input="calcularTotal" class="w-32 px-3 py-2 border border-gray-300 rounded-lg">
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-end">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                        <p class="text-sm text-blue-800">Total Aplicado: <span class="font-bold" x-text="'B/. ' + totalAplicado.toFixed(2)"></span></p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('cobros.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-check-circle mr-2"></i>Registrar Cobro
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function cobroForm() {
    return {
        clienteId: '{{ request("cliente_id") ?? "" }}',
        montoTotal: 0,
        facturasPendientes: @json($facturasPendientes ?? []),
        totalAplicado: 0,
        
        init() {
            if (this.clienteId) {
                this.cargarFacturas();
            }
        },
        
        cargarFacturas() {
            if (!this.clienteId) {
                this.facturasPendientes = [];
                return;
            }
            
            window.location.href = '{{ route("cobros.create") }}?cliente_id=' + this.clienteId;
        },
        
        calcularTotal() {
            this.totalAplicado = this.facturasPendientes.reduce((sum, factura) => {
                return sum + (parseFloat(factura.monto_aplicado) || 0);
            }, 0);
        },
        
        validateForm(e) {
            if (this.totalAplicado === 0) {
                e.preventDefault();
                alert('Debe aplicar el cobro a al menos una factura');
                return false;
            }
            
            if (this.totalAplicado > parseFloat(this.montoTotal)) {
                e.preventDefault();
                alert('El monto aplicado no puede ser mayor al monto total');
                return false;
            }
        }
    }
}
</script>
@endpush
@endsection
