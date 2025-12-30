@extends('layouts.app')

@section('title', 'Registrar Cobro')
@section('page-title', 'Registrar Cobro')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6" x-data="cobroForm()">
        <form method="POST" action="{{ route('cobros.store') }}" enctype="multipart/form-data" @submit="validarFormulario">
            @csrf
            
            <!-- Información General -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    <i class="bi bi-info-circle mr-2"></i>Información del Cobro
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Fecha *</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha') border-red-500 @enderror">
                        @error('fecha')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Cliente *</label>
                        <select name="cliente_id" x-model="clienteId" @change="cargarFacturas()" required 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cliente_id') border-red-500 @enderror">
                            <option value="">Seleccionar cliente...</option>
                            @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ (old('cliente_id') == $cliente->id || (isset($cliente) && $cliente->id == old('cliente_id'))) ? 'selected' : '' }}>
                                {{ $cliente->nombre_comercial }} - {{ $cliente->identificacion }}
                            </option>
                            @endforeach
                        </select>
                        @error('cliente_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Método de Pago -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">
                    <i class="bi bi-credit-card mr-2"></i>Método de Pago
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Pago *</label>
                        <select name="tipo_pago" x-model="tipoPago" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccionar...</option>
                            <option value="efectivo">💵 Efectivo</option>
                            <option value="cheque">📝 Cheque</option>
                            <option value="transferencia">🏦 Transferencia Bancaria</option>
                            <option value="tarjeta">💳 Tarjeta de Crédito/Débito</option>
                        </select>
                    </div>
                    
                    <div x-show="tipoPago && tipoPago !== 'efectivo'">
                        <label class="block text-sm font-medium text-slate-700 mb-2">
                            <span x-show="tipoPago === 'cheque'">N° de Cheque</span>
                            <span x-show="tipoPago === 'transferencia'">N° de Referencia</span>
                            <span x-show="tipoPago === 'tarjeta'">N° de Autorización</span>
                        </label>
                        <input type="text" name="referencia" value="{{ old('referencia') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="Número de referencia">
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div x-show="tipoPago === 'cheque' || tipoPago === 'transferencia'">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Banco</label>
                        <input type="text" name="banco" value="{{ old('banco') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="Nombre del banco">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Comprobante (Opcional)</label>
                        <input type="file" name="comprobante" accept="image/*,.pdf" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-slate-500">Imagen o PDF del comprobante</p>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Observaciones</label>
                    <textarea name="observaciones" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('observaciones') }}</textarea>
                </div>
            </div>
			
			<!-- Factoring (despu�s de la secci�n de M�todo de Pago) -->
<div class="mb-6">
    <h3 class="text-lg font-semibold text-slate-800 mb-4">
        <i class="bi bi-building mr-2"></i>Factoring (Opcional)
    </h3>
    
    <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
        <label class="flex items-center space-x-2 cursor-pointer">
            <input type="checkbox" name="es_factoring" value="1" x-model="esFactoring" class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
            <span class="font-medium text-purple-900">Este cobro es por Factoring</span>
        </label>
        <p class="text-xs text-purple-700 mt-1 ml-6">
            El factoring permite cobrar antes del vencimiento con un descuento financiero
        </p>
    </div>

    <div x-show="esFactoring" x-transition class="mt-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Financiera *</label>
                <input type="text" name="financiera" value="{{ old('financiera') }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                       placeholder="Nombre de la financiera">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Porcentaje Descuento (%) *</label>
                <input type="number" name="porcentaje_factoring" step="0.01" min="0" max="100"
                       x-model="porcentajeFactoring"
                       @input="calcularDescuentoFactoring()"
                       value="{{ old('porcentaje_factoring', 5) }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                       placeholder="5.00">
                <p class="text-xs text-slate-500 mt-1">T�picamente entre 3% y 7%</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Descuento B/.</label>
                <input type="number" name="descuento_factoring" step="0.01" min="0"
                       x-model="descuentoFactoring"
                       readonly
                       class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg"
                       placeholder="0.00">
            </div>
        </div>

        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-yellow-700 font-medium">Total Facturas:</p>
                    <p class="text-2xl font-bold text-yellow-900">B/. <span x-text="totalAplicado.toFixed(2)">0.00</span></p>
                </div>
                <div>
                    <p class="text-red-700 font-medium">Descuento Financiero:</p>
                    <p class="text-2xl font-bold text-red-700">- B/. <span x-text="descuentoFactoring">0.00</span></p>
                </div>
                <div>
                    <p class="text-green-700 font-medium">Monto Real Recibido:</p>
                    <p class="text-2xl font-bold text-green-700">B/. <span x-text="montoRealRecibido.toFixed(2)">0.00</span></p>
                </div>
            </div>
            <p class="text-xs text-yellow-700 mt-2">
                <i class="bi bi-info-circle mr-1"></i>
                El descuento se registrar� como gasto financiero. Las facturas se aplicar�n por el monto total.
            </p>
        </div>
    </div>
</div>

            <!-- Facturas del Cliente (se cargan dinámicamente) -->
            <div class="mb-6" x-show="clienteId">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-slate-800">
                        <i class="bi bi-receipt mr-2"></i>Facturas Pendientes
                    </h3>
                    <div x-show="cargandoFacturas" class="flex items-center text-blue-600">
                        <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Cargando facturas...
                    </div>
                </div>

                @if(isset($cliente) && $facturasPendientes->count() > 0)
                <div class="space-y-3">
                    @foreach($facturasPendientes as $index => $factura)
                    <div class="border border-gray-200 rounded-lg hover:border-blue-300 transition"
                         :class="facturasSeleccionadas[{{ $index }}] ? 'bg-blue-50 border-blue-400 ring-2 ring-blue-200' : 'bg-white'">
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-4">
                                <!-- Checkbox y detalles de factura -->
                                <label class="flex items-start space-x-3 flex-1 cursor-pointer">
                                    <input type="checkbox" 
                                           name="facturas[{{ $index }}][incluir]" 
                                           value="1"
                                           x-model="facturasSeleccionadas[{{ $index }}]"
                                           @change="toggleFactura({{ $index }}, {{ $factura->saldo_pendiente }})"
                                           class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 mt-1">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <span class="font-mono font-bold text-blue-700 text-lg">{{ $factura->numero }}</span>
                                                <span class="ml-3 text-sm text-slate-600">
                                                    <i class="bi bi-calendar3"></i> {{ $factura->fecha->format('d/m/Y') }}
                                                </span>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-xs text-slate-500">Total Factura</div>
                                                <div class="font-bold text-slate-700">B/. {{ number_format($factura->total, 2) }}</div>
                                            </div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-3 text-sm">
                                            <div>
                                                <span class="text-slate-600">Estado:</span>
                                                @if($factura->estado === 'pendiente')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 ml-1">
                                                    <i class="bi bi-clock mr-1"></i> Pendiente
                                                </span>
                                                @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-1">
                                                    <i class="bi bi-hourglass-split mr-1"></i> Parcial
                                                </span>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <span class="text-slate-600">Días vencido:</span>
                                                <span class="font-semibold {{ $factura->fecha->diffInDays(now()) > 30 ? 'text-red-600' : 'text-slate-700' }}">
                                                    {{ $factura->fecha->diffInDays(now()) }} días
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-2 pt-2 border-t border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium text-slate-700">Saldo Pendiente:</span>
                                                <span class="text-xl font-bold text-red-600">B/. {{ number_format($factura->saldo_pendiente, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                                
                                <!-- Input de monto a aplicar -->
                                <div x-show="facturasSeleccionadas[{{ $index }}]" 
                                     x-transition
                                     class="flex flex-col items-end space-y-2 min-w-[200px]">
                                    <input type="hidden" name="facturas[{{ $index }}][factura_id]" value="{{ $factura->id }}">
                                    
                                    <div class="w-full">
                                        <label class="block text-xs font-medium text-slate-700 mb-1">Monto a Aplicar (B/.)</label>
                                        <input type="number" 
                                               name="facturas[{{ $index }}][monto_aplicado]" 
                                               step="0.01"
                                               min="0.01"
                                               max="{{ $factura->saldo_pendiente }}"
                                               x-model="montosAplicados[{{ $index }}]"
                                               @input="calcularTotales()"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-right font-bold text-lg focus:ring-2 focus:ring-blue-500"
                                               placeholder="0.00">
                                        <p class="mt-1 text-xs text-slate-500">Máximo: B/. {{ number_format($factura->saldo_pendiente, 2) }}</p>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button type="button" 
                                                @click="montosAplicados[{{ $index }}] = ({{ $factura->saldo_pendiente }} / 2).toFixed(2); calcularTotales()"
                                                class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                            50%
                                        </button>
                                        <button type="button" 
                                                @click="montosAplicados[{{ $index }}] = {{ $factura->saldo_pendiente }}; calcularTotales()"
                                                class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200 transition">
                                            100%
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="text-slate-400">
                        <i class="bi bi-inbox text-5xl mb-3 block"></i>
                        <p class="text-lg font-medium" x-show="!clienteId">Selecciona un cliente para ver sus facturas pendientes</p>
                        <p class="text-lg font-medium" x-show="clienteId && !cargandoFacturas">Este cliente no tiene facturas pendientes</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Resumen del Cobro -->
            <div class="mb-6" x-show="totalAplicado > 0">
                <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-6 border-2 border-blue-200">
                    <h3 class="text-lg font-semibold text-slate-800 mb-4">
                        <i class="bi bi-calculator mr-2"></i>Resumen del Cobro
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-700">Facturas seleccionadas:</span>
                            <span class="font-bold text-blue-600" x-text="Object.values(facturasSeleccionadas).filter(v => v).length"></span>
                        </div>
                        
                        <div class="flex justify-between items-center text-lg border-t pt-3">
                            <span class="font-semibold text-slate-800">Total a Cobrar:</span>
                            <span class="text-3xl font-bold text-green-600">
                                B/. <span x-text="totalAplicado.toFixed(2)">0.00</span>
                            </span>
                        </div>
                        
                        <input type="hidden" name="monto" :value="totalAplicado.toFixed(2)">
                        
                        <div class="pt-3 border-t">
                            <div class="flex items-center text-sm" :class="validacionMonto ? 'text-green-700' : 'text-amber-700'">
                                <i class="mr-2" :class="validacionMonto ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-triangle-fill'"></i>
                                <span x-text="mensajeValidacion"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Botones -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('cobros.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                    <i class="bi bi-x-circle mr-2"></i>Cancelar
                </a>
                <button type="submit" 
                        :disabled="!validacionMonto || totalAplicado == 0"
                        :class="validacionMonto && totalAplicado > 0 ? 'bg-green-600 hover:bg-green-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
                        class="px-6 py-2 text-white rounded-lg transition shadow-sm hover:shadow-md">
                    <i class="bi bi-save mr-2"></i>Registrar Cobro
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function cobroForm() {
    return {
        clienteId: '{{ old("cliente_id") ?? (isset($cliente) ? $cliente->id : "") }}',
        tipoPago: '{{ old("tipo_pago", "") }}',
        facturasSeleccionadas: {},
        montosAplicados: {},
        totalAplicado: 0,
        cargandoFacturas: false,
        validacionMonto: true,
        mensajeValidacion: '',
        esFactoring: {{ old('es_factoring', 0) ? 'true' : 'false' }},
        porcentajeFactoring: {{ old('porcentaje_factoring', 5) }},
        descuentoFactoring: 0,
        montoRealRecibido: 0,
        
        init() {
            @if(isset($facturasPendientes))
            @foreach($facturasPendientes as $index => $factura)
            this.montosAplicados[{{ $index }}] = {{ old('facturas.'.$index.'.monto_aplicado', $factura->saldo_pendiente) }};
            @if(old('facturas.'.$index.'.incluir'))
            this.facturasSeleccionadas[{{ $index }}] = true;
            @endif
            @endforeach
            @endif
            
            this.calcularTotales();
        },
        
        cargarFacturas() {
            if (this.clienteId) {
                this.cargandoFacturas = true;
                window.location.href = '{{ route("cobros.create") }}?cliente_id=' + this.clienteId;
            }
        },
        
        toggleFactura(index, saldoPendiente) {
            if (this.facturasSeleccionadas[index]) {
                if (!this.montosAplicados[index] || this.montosAplicados[index] == 0) {
                    this.montosAplicados[index] = saldoPendiente;
                }
            } else {
                this.montosAplicados[index] = 0;
            }
            this.calcularTotales();
        },
        
        calcularTotales() {
            this.totalAplicado = 0;
            
            Object.keys(this.facturasSeleccionadas).forEach(index => {
                if (this.facturasSeleccionadas[index]) {
                    const monto = parseFloat(this.montosAplicados[index] || 0);
                    this.totalAplicado += monto;
                }
            });
            
            this.calcularDescuentoFactoring();
            this.validarMontos();
        },
        
        calcularDescuentoFactoring() {
            if (this.esFactoring && this.totalAplicado > 0) {
                this.descuentoFactoring = (this.totalAplicado * this.porcentajeFactoring / 100).toFixed(2);
                this.montoRealRecibido = this.totalAplicado - parseFloat(this.descuentoFactoring);
            } else {
                this.descuentoFactoring = 0;
                this.montoRealRecibido = this.totalAplicado;
            }
        },
        
        validarMontos() {
            const facturasConMonto = Object.keys(this.facturasSeleccionadas).filter(index => {
                return this.facturasSeleccionadas[index] && parseFloat(this.montosAplicados[index] || 0) > 0;
            });
            
            if (facturasConMonto.length === 0) {
                this.validacionMonto = false;
                this.mensajeValidacion = 'Selecciona al menos una factura y asigna un monto';
                return;
            }
            
            if (this.esFactoring) {
                this.mensajeValidacion = `Factoring: B/. ${this.totalAplicado.toFixed(2)} - Descuento: B/. ${this.descuentoFactoring} = Recibir�s: B/. ${this.montoRealRecibido.toFixed(2)}`;
            } else {
                this.mensajeValidacion = `Listo para cobrar B/. ${this.totalAplicado.toFixed(2)} aplicados a ${facturasConMonto.length} factura(s)`;
            }
            
            this.validacionMonto = true;
        },
        
        validarFormulario(e) {
            if (!this.validacionMonto || this.totalAplicado == 0) {
                e.preventDefault();
                alert('Debes seleccionar al menos una factura y asignar montos v�lidos');
                return false;
            }
            
            if (!this.tipoPago) {
                e.preventDefault();
                alert('Debes seleccionar un m�todo de pago');
                return false;
            }
            
            return true;
        }
    }
}
</script>
@endpush
@endsection