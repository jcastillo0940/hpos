@extends('layouts.app')

@section('title', 'Nuevo Pago')
@section('page-title', 'Nuevo Pago')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-slate-600 mb-4">
        <a href="{{ route('pagos.index') }}" class="hover:text-blue-600">Pagos</a>
        <i class="bi bi-chevron-right"></i>
        <span class="text-slate-800 font-medium">Nuevo Pago</span>
    </div>
    <h3 class="text-2xl font-bold text-slate-800">Registrar Nuevo Pago</h3>
</div>

<form action="{{ route('pagos.store') }}" method="POST" x-data="pagoForm()">
    @csrf
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h4 class="text-lg font-semibold text-slate-800 mb-4">Información del Pago</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Fecha <span class="text-red-500">*</span>
                </label>
                <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha') border-red-500 @enderror">
                @error('fecha')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Proveedor <span class="text-red-500">*</span>
                </label>
                <select name="proveedor_id" x-model="proveedorId" @change="cargarFacturas()" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('proveedor_id') border-red-500 @enderror">
                    <option value="">Seleccionar proveedor</option>
                    @foreach($proveedores as $proveedor)
                        <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                            {{ $proveedor->razon_social }}
                        </option>
                    @endforeach
                </select>
                @error('proveedor_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Monto Total <span class="text-red-500">*</span>
                </label>
                <input type="number" name="monto" x-model="montoPago" step="0.01" min="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('monto') border-red-500 @enderror">
                @error('monto')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Tipo de Pago <span class="text-red-500">*</span>
                </label>
                <select name="tipo_pago" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tipo_pago') border-red-500 @enderror">
                    <option value="">Seleccionar tipo</option>
                    <option value="efectivo" {{ old('tipo_pago') == 'efectivo' ? 'selected' : '' }}>Efectivo</option>
                    <option value="cheque" {{ old('tipo_pago') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                    <option value="transferencia" {{ old('tipo_pago') == 'transferencia' ? 'selected' : '' }}>Transferencia</option>
                    <option value="tarjeta" {{ old('tipo_pago') == 'tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                </select>
                @error('tipo_pago')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Referencia / N° Cheque
                </label>
                <input type="text" name="referencia" value="{{ old('referencia') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Banco
                </label>
                <input type="text" name="banco" value="{{ old('banco') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-2">
                    Observaciones
                </label>
                <textarea name="observaciones" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('observaciones') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Facturas Pendientes -->
    <div x-show="facturas.length > 0" class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-semibold text-slate-800">Facturas Pendientes del Proveedor</h4>
            <button type="button" @click="aplicarTodo()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition">
                <i class="bi bi-check-all mr-2"></i>Aplicar a Todo
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                            <input type="checkbox" @change="seleccionarTodas($event.target.checked)" class="rounded border-gray-300">
                        </th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">N° Factura</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Saldo Pendiente</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Monto a Aplicar</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(factura, index) in facturas" :key="index">
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3">
                                <input type="checkbox" x-model="factura.seleccionada" @change="actualizarMontoAplicar(index)" class="rounded border-gray-300">
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-mono text-sm font-medium" x-text="factura.numero_factura"></span>
                            </td>
                            <td class="px-4 py-3 text-sm" x-text="factura.fecha"></td>
                            <td class="px-4 py-3">
                                <span class="font-medium" x-text="'B/. ' + parseFloat(factura.total).toFixed(2)"></span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-bold text-red-600" x-text="'B/. ' + parseFloat(factura.saldo_pendiente).toFixed(2)"></span>
                            </td>
                            <td class="px-4 py-3">
                                <input 
                                    type="hidden" 
                                    :name="'facturas['+index+'][factura_id]'" 
                                    :value="factura.id"
                                    x-show="factura.seleccionada && factura.monto_aplicar > 0">
                                <input 
                                    type="number" 
                                    :name="'facturas['+index+'][monto_aplicado]'" 
                                    x-model="factura.monto_aplicar"
                                    x-show="factura.seleccionada"
                                    @input="validarMonto(index)"
                                    :max="factura.saldo_pendiente"
                                    step="0.01"
                                    min="0.01"
                                    class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="factura.monto_aplicar > parseFloat(factura.saldo_pendiente) ? 'border-red-500' : ''">
                                <span x-show="!factura.seleccionada" class="text-slate-400">-</span>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold">Total a Aplicar:</td>
                        <td class="px-4 py-3">
                            <span class="text-xl font-bold" :class="totalAplicar > montoPago ? 'text-red-600' : 'text-green-600'" x-text="'B/. ' + totalAplicar.toFixed(2)"></span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-3 text-right font-semibold">Diferencia:</td>
                        <td class="px-4 py-3">
                            <span class="text-lg font-bold" :class="diferencia < 0 ? 'text-red-600' : 'text-blue-600'" x-text="'B/. ' + diferencia.toFixed(2)"></span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div x-show="totalAplicar > montoPago" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 text-sm">
                <i class="bi bi-exclamation-triangle mr-2"></i>
                El monto aplicado excede el monto del pago. Ajusta los montos antes de guardar.
            </p>
        </div>
    </div>

    <!-- Mensaje cuando no hay proveedor seleccionado -->
    <div x-show="!proveedorId" class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-12 text-center">
        <i class="bi bi-inbox text-6xl text-gray-400 mb-4"></i>
        <p class="text-lg font-medium text-gray-600 mb-2">Selecciona un Proveedor</p>
        <p class="text-sm text-gray-500">Elige un proveedor para ver sus facturas pendientes</p>
    </div>

    <!-- Mensaje cuando no hay facturas -->
    <div x-show="proveedorId && facturas.length === 0 && !cargando" class="bg-green-50 border border-green-200 rounded-xl p-12 text-center">
        <i class="bi bi-check-circle text-6xl text-green-500 mb-4"></i>
        <p class="text-lg font-medium text-green-800 mb-2">Sin Facturas Pendientes</p>
        <p class="text-sm text-green-600">Este proveedor no tiene facturas pendientes de pago</p>
    </div>

    <!-- Botones de Acción -->
    <div x-show="facturas.length > 0" class="flex items-center space-x-4">
        <button type="submit" :disabled="totalAplicar > montoPago || totalAplicar <= 0" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
            <i class="bi bi-save mr-2"></i>
            Registrar Pago
        </button>
        <a href="{{ route('pagos.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition">
            <i class="bi bi-x-circle mr-2"></i>
            Cancelar
        </a>
    </div>
</form>

@push('scripts')
<script>
function pagoForm() {
    return {
        proveedorId: '{{ old('proveedor_id', '') }}',
        montoPago: {{ old('monto', 0) }},
        facturas: [],
        cargando: false,
        
        get totalAplicar() {
            return this.facturas
                .filter(f => f.seleccionada)
                .reduce((sum, f) => sum + (parseFloat(f.monto_aplicar) || 0), 0);
        },
        
        get diferencia() {
            return this.montoPago - this.totalAplicar;
        },
        
        async cargarFacturas() {
            if (!this.proveedorId) {
                this.facturas = [];
                return;
            }
            
            this.cargando = true;
            
            try {
                const response = await fetch(`/api/facturas-pendientes/${this.proveedorId}`);
                const data = await response.json();
                
                this.facturas = data.map(factura => ({
                    id: factura.id,
                    numero_factura: factura.numero_factura,
                    fecha: factura.fecha,
                    total: factura.total,
                    saldo_pendiente: factura.saldo_pendiente,
                    seleccionada: false,
                    monto_aplicar: 0
                }));
            } catch (error) {
                console.error('Error al cargar facturas:', error);
                alert('Error al cargar las facturas pendientes');
            } finally {
                this.cargando = false;
            }
        },
        
        seleccionarTodas(checked) {
            this.facturas.forEach(factura => {
                factura.seleccionada = checked;
                if (checked) {
                    factura.monto_aplicar = factura.saldo_pendiente;
                } else {
                    factura.monto_aplicar = 0;
                }
            });
        },
        
        actualizarMontoAplicar(index) {
            const factura = this.facturas[index];
            if (factura.seleccionada) {
                factura.monto_aplicar = factura.saldo_pendiente;
            } else {
                factura.monto_aplicar = 0;
            }
        },
        
        validarMonto(index) {
            const factura = this.facturas[index];
            if (parseFloat(factura.monto_aplicar) > parseFloat(factura.saldo_pendiente)) {
                factura.monto_aplicar = factura.saldo_pendiente;
            }
        },
        
        aplicarTodo() {
            let disponible = this.montoPago;
            
            this.facturas.forEach(factura => {
                if (disponible > 0) {
                    const aplicar = Math.min(disponible, parseFloat(factura.saldo_pendiente));
                    factura.seleccionada = true;
                    factura.monto_aplicar = aplicar;
                    disponible -= aplicar;
                } else {
                    factura.seleccionada = false;
                    factura.monto_aplicar = 0;
                }
            });
        },
        
        init() {
            if (this.proveedorId) {
                this.cargarFacturas();
            }
        }
    }
}
</script>
@endpush
@endsection
