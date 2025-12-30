@extends('layouts.app')

@section('title', 'Crear Nota de Crédito')
@section('page-title', 'Crear Nota de Crédito')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6" x-data="notaCreditoForm()">
        <form method="POST" action="{{ route('notas-credito.store') }}">
            @csrf
            
            <!-- Información General -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">Información General</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Fecha *</label>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('fecha') border-red-500 @enderror">
                        @error('fecha')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Tipo de Nota *</label>
                        <select name="tipo" x-model="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('tipo') border-red-500 @enderror">
                            <option value="">Seleccionar tipo...</option>
                            <option value="devolucion" {{ old('tipo') === 'devolucion' ? 'selected' : '' }}>Devolución de Mercancía</option>
                            <option value="descuento" {{ old('tipo') === 'descuento' ? 'selected' : '' }}>Descuento/Rebaja</option>
                            <option value="ajuste" {{ old('tipo') === 'ajuste' ? 'selected' : '' }}>Ajuste de Facturación</option>
                        </select>
                        @error('tipo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Motivo específico según el tipo -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Motivo Específico *</label>
                    
                    <!-- Motivos para Devolución -->
                    <select x-show="tipo === 'devolucion'" name="motivo_devolucion" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar motivo...</option>
                        <option value="producto_dañado">Producto Dañado</option>
                        <option value="producto_vencido">Producto Vencido</option>
                        <option value="producto_defectuoso">Producto Defectuoso</option>
                        <option value="error_envio">Error en el Envío</option>
                        <option value="cliente_insatisfecho">Cliente Insatisfecho</option>
                        <option value="producto_incorrecto">Producto Incorrecto</option>
                        <option value="sobrante">Sobrante de Mercancía</option>
                        <option value="otro">Otro Motivo</option>
                    </select>

                    <!-- Motivos para Descuento -->
                    <select x-show="tipo === 'descuento'" name="motivo_descuento" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar motivo...</option>
                        <option value="descuento_comercial">Descuento Comercial</option>
                        <option value="descuento_promocional">Descuento Promocional</option>
                        <option value="descuento_volumen">Descuento por Volumen</option>
                        <option value="descuento_pronto_pago">Descuento Pronto Pago</option>
                        <option value="ajuste_precio">Ajuste de Precio</option>
                        <option value="cortesia">Cortesía/Bonificación</option>
                        <option value="compensacion">Compensación</option>
                    </select>

                    <!-- Motivos para Ajuste -->
                    <select x-show="tipo === 'ajuste'" name="motivo_ajuste" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Seleccionar motivo...</option>
                        <option value="error_facturacion">Error de Facturación</option>
                        <option value="duplicacion_cobro">Duplicación de Cobro</option>
                        <option value="error_precio">Error en Precio Facturado</option>
                        <option value="error_cantidad">Error en Cantidad</option>
                        <option value="error_itbms">Error en ITBMS</option>
                        <option value="ajuste_administrativo">Ajuste Administrativo</option>
                    </select>
                </div>

                <!-- Cliente (SIEMPRE visible) -->
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cliente *</label>
                    <select name="cliente_id" x-model="clienteSeleccionado" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('cliente_id') border-red-500 @enderror">
                        <option value="">Seleccionar cliente...</option>
                        @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ (old('cliente_id') == $cliente->id || (isset($factura) && $factura->cliente_id == $cliente->id)) ? 'selected' : '' }}>
                            {{ $cliente->nombre_comercial }} - {{ $cliente->identificacion }}
                        </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Factura (Opcional)</label>
                    <select name="factura_id" x-model="facturaId" @change="cargarFactura()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('factura_id') border-red-500 @enderror">
                        <option value="">Sin factura asociada...</option>
                        @foreach($facturas as $f)
                        <option value="{{ $f->id }}" {{ (old('factura_id') == $f->id || (isset($factura) && $factura->id == $f->id)) ? 'selected' : '' }}>
                            {{ $f->numero }} - {{ $f->cliente->nombre_comercial }} (Saldo: B/. {{ number_format($f->saldo_pendiente, 2) }})
                        </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">
                        <i class="bi bi-info-circle mr-1"></i>
                        Asocia esta nota a una factura específica o déjala general para aplicar al saldo total del cliente
                    </p>
                    @error('factura_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Descripción Adicional *</label>
                    <textarea name="observaciones" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('observaciones') border-red-500 @enderror" placeholder="Describe el motivo detallado de la nota de crédito...">{{ old('observaciones') }}</textarea>
                    @error('observaciones')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Información de la factura seleccionada -->
            @if(isset($factura))
            <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="font-semibold text-blue-900 mb-3">
                    <i class="bi bi-receipt mr-2"></i>Factura {{ $factura->numero }}
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm mb-4">
                    <div>
                        <span class="font-medium text-blue-700">Cliente:</span>
                        <p class="text-blue-900">{{ $factura->cliente->nombre_comercial }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-blue-700">Fecha:</span>
                        <p class="text-blue-900">{{ $factura->fecha->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-blue-700">Total:</span>
                        <p class="text-blue-900 font-bold">B/. {{ number_format($factura->total, 2) }}</p>
                    </div>
                    <div>
                        <span class="font-medium text-blue-700">Saldo Pendiente:</span>
                        <p class="text-blue-900 font-bold">B/. {{ number_format($factura->saldo_pendiente, 2) }}</p>
                    </div>
                </div>

                <!-- Productos de la factura para devolución -->
                <div x-show="tipo === 'devolucion'">
                    <h5 class="font-semibold text-blue-900 mb-3 flex items-center">
                        <i class="bi bi-box-seam mr-2"></i>
                        Seleccionar Productos a Devolver
                        <span class="ml-2 text-xs font-normal text-blue-600">(Marca los productos y ajusta la cantidad)</span>
                    </h5>
                    <div class="space-y-2">
                        @foreach($factura->detalles as $index => $detalle)
                        <div class="bg-white p-3 rounded border border-blue-100 hover:border-blue-300 transition" 
                             :class="productosSeleccionados[{{ $index }}] ? 'ring-2 ring-blue-500' : ''">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 min-w-0">
                                    <label class="flex items-start space-x-3 cursor-pointer">
                                        <input type="checkbox" 
                                               name="detalles[{{ $index }}][incluir]" 
                                               value="1"
                                               x-model="productosSeleccionados[{{ $index }}]"
                                               @change="calcularTotalDevolucion()"
                                               class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 mt-1">
                                        <div class="flex-1">
                                            <span class="font-medium text-slate-800 block">{{ $detalle->producto->nombre }}</span>
                                            <div class="text-xs text-slate-500 mt-1 space-y-0.5">
                                                <p>Código: <span class="font-mono">{{ $detalle->producto->codigo }}</span></p>
                                                <p>Cantidad facturada: <strong>{{ number_format($detalle->cantidad, 2) }}</strong> unidades</p>
                                                <p>Precio unitario: <strong>B/. {{ number_format($detalle->precio_unitario, 2) }}</strong></p>
                                                <p>Total línea: <strong>B/. {{ number_format($detalle->total, 2) }}</strong></p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                
                                <div x-show="productosSeleccionados[{{ $index }}]" 
                                     x-transition
                                     class="flex flex-col items-end space-y-2">
                                    <input type="hidden" name="detalles[{{ $index }}][factura_detalle_id]" value="{{ $detalle->id }}">
                                    <div class="flex items-center space-x-2">
                                        <label class="text-xs font-medium text-slate-700">Cantidad a devolver:</label>
                                        <input type="number" 
                                               name="detalles[{{ $index }}][cantidad]" 
                                               x-model="cantidadesDevolucion[{{ $index }}]"
                                               @input="calcularTotalDevolucion()"
                                               step="0.01"
                                               min="0.01" 
                                               max="{{ $detalle->cantidad }}"
                                               value="{{ old('detalles.'.$index.'.cantidad', $detalle->cantidad) }}"
                                               placeholder="Cantidad"
                                               class="w-28 px-3 py-2 border border-gray-300 rounded text-sm font-medium focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    <p class="text-xs text-slate-500">Máximo: {{ number_format($detalle->cantidad, 2) }}</p>
                                    <div class="text-right">
                                        <p class="text-xs text-slate-600">Subtotal devolución:</p>
                                        <p class="text-sm font-bold text-blue-600">
                                            B/. <span x-text="calcularLineaDevolucion({{ $index }}, {{ $detalle->precio_unitario }}, {{ $detalle->itbms_porcentaje }})"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <!-- Resumen de devolución -->
                    <div class="mt-4 p-3 bg-blue-100 rounded border border-blue-300" x-show="Object.values(productosSeleccionados).some(v => v)">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-blue-900">Total de Devolución:</span>
                            <span class="text-xl font-bold text-blue-900">B/. <span x-text="totalDevolucion.toFixed(2)">0.00</span></span>
                        </div>
                    </div>
                    
                    <p class="mt-2 text-xs text-blue-700 flex items-start">
                        <i class="bi bi-info-circle mr-1 mt-0.5"></i>
                        <span>Los productos marcados se devolverán al inventario automáticamente cuando se aplique la nota de crédito</span>
                    </p>
                </div>

                <!-- Monto de descuento/ajuste -->
                <div x-show="tipo === 'descuento' || tipo === 'ajuste'">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="bi bi-cash-coin mr-1"></i>
                        Monto del <span x-text="tipo === 'descuento' ? 'Descuento' : 'Ajuste'"></span> (B/.) *
                    </label>
                    <div class="flex items-center space-x-4">
                        <input type="number" 
                               name="monto_descuento" 
                               step="0.01" 
                               min="0.01" 
                               max="{{ $factura->saldo_pendiente }}"
                               value="{{ old('monto_descuento') }}"
                               @input="montoDescuento = $event.target.value"
                               class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <span class="text-sm text-slate-600 whitespace-nowrap">
                            Máximo: <strong class="text-blue-600">B/. {{ number_format($factura->saldo_pendiente, 2) }}</strong>
                        </span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">
                        <i class="bi bi-calculator mr-1"></i>
                        Este monto se descontará del saldo pendiente del cliente
                    </p>
                    
                    <!-- Vista previa del monto -->
                    <div class="mt-3 p-3 bg-green-50 rounded border border-green-200" x-show="montoDescuento > 0">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-green-900">Crédito a aplicar:</span>
                            <span class="text-xl font-bold text-green-900">B/. <span x-text="parseFloat(montoDescuento || 0).toFixed(2)">0.00</span></span>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Si no hay factura seleccionada - Nota de crédito general -->
            <div class="mb-6 p-4 bg-purple-50 rounded-lg border border-purple-200" x-show="!facturaId">
                <h4 class="font-semibold text-purple-900 mb-2 flex items-center">
                    <i class="bi bi-info-circle mr-2"></i>
                    Nota de Crédito General (Sin Factura)
                </h4>
                <p class="text-purple-800 text-sm mb-4">
                    Esta nota de crédito se aplicará al saldo total del cliente seleccionado, sin estar vinculada a una factura específica.
                </p>

                <div x-show="tipo === 'descuento' || tipo === 'ajuste'">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="bi bi-cash-coin mr-1"></i>
                        Monto del <span x-text="tipo === 'descuento' ? 'Descuento' : 'Ajuste'"></span> (B/.) *
                    </label>
                    <input type="number" 
                           name="monto_descuento" 
                           step="0.01" 
                           min="0.01"
                           value="{{ old('monto_descuento') }}"
                           @input="montoDescuento = $event.target.value"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                           placeholder="0.00">
                    <p class="mt-1 text-xs text-slate-500">Ingresa el monto del crédito a favor del cliente</p>
                    
                    <!-- Vista previa -->
                    <div class="mt-3 p-3 bg-green-50 rounded border border-green-200" x-show="montoDescuento > 0">
                        <div class="flex justify-between items-center">
                            <span class="font-semibold text-green-900">Crédito a aplicar:</span>
                            <span class="text-xl font-bold text-green-900">B/. <span x-text="parseFloat(montoDescuento || 0).toFixed(2)">0.00</span></span>
                        </div>
                    </div>
                </div>

                <div x-show="tipo === 'devolucion'" class="text-amber-700 text-sm flex items-start mt-3">
                    <i class="bi bi-exclamation-triangle mr-2 mt-0.5"></i>
                    <span>Para devoluciones de productos, es recomendable asociar una factura específica.</span>
                </div>
            </div>
            @endif
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('notas-credito.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">
                    <i class="bi bi-x-circle mr-2"></i>Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm hover:shadow-md">
                    <i class="bi bi-save mr-2"></i>Crear Nota de Crédito
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function notaCreditoForm() {
    return {
        tipo: '{{ old("tipo", "") }}',
        facturaId: '{{ request("factura_id") ?? old("factura_id") ?? "" }}',
        clienteSeleccionado: '{{ old("cliente_id") ?? (isset($factura) ? $factura->cliente_id : "") }}',
        productosSeleccionados: {},
        cantidadesDevolucion: {},
        totalDevolucion: 0,
        montoDescuento: {{ old('monto_descuento', 0) }},
        
        cargarFactura() {
            if (this.facturaId) {
                window.location.href = '{{ route("notas-credito.create") }}?factura_id=' + this.facturaId;
            }
        },
        
        calcularLineaDevolucion(index, precioUnitario, itbmsPorcentaje) {
            if (!this.productosSeleccionados[index]) return '0.00';
            
            const cantidad = parseFloat(this.cantidadesDevolucion[index] || 0);
            const subtotal = cantidad * precioUnitario;
            const itbms = subtotal * (itbmsPorcentaje / 100);
            const total = subtotal + itbms;
            
            return total.toFixed(2);
        },
        
        calcularTotalDevolucion() {
            this.totalDevolucion = 0;
            
            @if(isset($factura))
            @foreach($factura->detalles as $index => $detalle)
            if (this.productosSeleccionados[{{ $index }}]) {
                const cantidad = parseFloat(this.cantidadesDevolucion[{{ $index }}] || {{ $detalle->cantidad }});
                const precioUnitario = {{ $detalle->precio_unitario }};
                const itbmsPorcentaje = {{ $detalle->itbms_porcentaje }};
                
                const subtotal = cantidad * precioUnitario;
                const itbms = subtotal * (itbmsPorcentaje / 100);
                this.totalDevolucion += subtotal + itbms;
            }
            @endforeach
            @endif
        }
    }
}
</script>
@endpush
@endsection