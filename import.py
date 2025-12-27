import os

def create_file(path, content):
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"✓ Created: {path}")

base_path = "resources/views"

create_file(f"{base_path}/ordenes-entrega/create.blade.php", """@extends('layouts.app')

@section('title', 'Nueva Orden de Entrega')
@section('page-title', 'Nueva Orden de Entrega')

@section('content')
<div class="max-w-6xl mx-auto" x-data="ordenEntrega()">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('ordenes-entrega.store') }}" @submit="prepareSubmit">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Cliente *</label>
                    <select name="cliente_id" required x-model="clienteId" @change="cargarSucursales" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Seleccionar cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre_comercial }}</option>
                        @endforeach
                    </select>
                </div>

                <div x-show="sucursales.length > 0">
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        <i class="bi bi-building text-blue-600"></i> Sucursal
                    </label>
                    <select name="cliente_sucursal_id" x-model="sucursalId" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Dirección principal</option>
                        <template x-for="sucursal in sucursales" :key="sucursal.id">
                            <option :value="sucursal.id" x-text="sucursal.nombre + ' - ' + sucursal.direccion"></option>
                        </template>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Fecha *</label>
                    <input type="date" name="fecha" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Observaciones</label>
                    <input type="text" name="observaciones" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="font-semibold text-slate-800">Productos</h4>
                    <button type="button" @click="agregarProducto" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition text-sm">
                        <i class="bi bi-plus-circle mr-2"></i>Agregar Producto
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Precio</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td class="px-4 py-2">
                                        <select :name="'detalles['+index+'][producto_id]'" required x-model="item.producto_id" @change="actualizarPrecio(index)" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <option value="">Seleccionar</option>
                                            @foreach($productos as $producto)
                                                <option value="{{ $producto->id }}" data-precio="{{ $producto->precio_venta }}">{{ $producto->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" :name="'detalles['+index+'][cantidad]'" required step="0.01" x-model="item.cantidad" @input="calcularSubtotal(index)" class="w-24 px-3 py-2 border border-gray-300 rounded-lg">
                                    </td>
                                    <td class="px-4 py-2">
                                        <input type="number" :name="'detalles['+index+'][precio_unitario]'" required step="0.01" x-model="item.precio_unitario" @input="calcularSubtotal(index)" class="w-32 px-3 py-2 border border-gray-300 rounded-lg">
                                    </td>
                                    <td class="px-4 py-2">
                                        <span class="font-bold" x-text="'B/. ' + item.subtotal.toFixed(2)"></span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <button type="button" @click="eliminarProducto(index)" class="text-red-600 hover:text-red-800">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="items.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">No hay productos agregados</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="text-right">
                    <p class="text-lg font-semibold">Total: <span class="text-blue-600" x-text="'B/. ' + calcularTotal().toFixed(2)"></span></p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('ordenes-entrega.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-slate-700 rounded-lg transition">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                        <i class="bi bi-check-circle mr-2"></i>Guardar Orden
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function ordenEntrega() {
    return {
        clienteId: '',
        sucursalId: '',
        sucursales: [],
        items: [],
        
        async cargarSucursales() {
            if (!this.clienteId) {
                this.sucursales = [];
                return;
            }
            
            try {
                const response = await fetch(`/clientes/${this.clienteId}/sucursales`);
                const data = await response.json();
                this.sucursales = data;
                this.sucursalId = '';
            } catch (error) {
                console.error('Error cargando sucursales:', error);
            }
        },
        
        agregarProducto() {
            this.items.push({
                producto_id: '',
                cantidad: 1,
                precio_unitario: 0,
                subtotal: 0
            });
        },
        
        eliminarProducto(index) {
            this.items.splice(index, 1);
        },
        
        actualizarPrecio(index) {
            const select = event.target;
            const precio = select.options[select.selectedIndex].dataset.precio;
            this.items[index].precio_unitario = parseFloat(precio);
            this.calcularSubtotal(index);
        },
        
        calcularSubtotal(index) {
            const item = this.items[index];
            item.subtotal = item.cantidad * item.precio_unitario;
        },
        
        calcularTotal() {
            return this.items.reduce((total, item) => total + item.subtotal, 0);
        },
        
        prepareSubmit(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Debe agregar al menos un producto');
            }
        }
    }
}
</script>
@endpush
@endsection
""")

create_file(f"{base_path}/ordenes-entrega/pdf.blade.php", """<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Entrega {{ $ordenEntrega->numero }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 10px;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #2563eb;
        }
        .document-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #2563eb;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }
        .total-label {
            font-weight: bold;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            color: #2563eb;
            border-top: 2px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $ordenEntrega->empresa->razon_social }}</div>
        <div>RUC: {{ $ordenEntrega->empresa->ruc }}</div>
        <div>{{ $ordenEntrega->empresa->direccion }}</div>
        <div>Tel: {{ $ordenEntrega->empresa->telefono }}</div>
        <div class="document-title">ORDEN DE ENTREGA</div>
        <div>{{ $ordenEntrega->numero }}</div>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Fecha:</span>
            <span>{{ $ordenEntrega->fecha->format('d/m/Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Cliente:</span>
            <span>{{ $ordenEntrega->cliente->nombre_comercial }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">RUC/Cédula:</span>
            <span>{{ $ordenEntrega->cliente->identificacion }}</span>
        </div>
        @if($ordenEntrega->clienteSucursal)
        <div class="info-row">
            <span class="info-label">Sucursal:</span>
            <span>{{ $ordenEntrega->clienteSucursal->nombre }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dirección Entrega:</span>
            <span>{{ $ordenEntrega->clienteSucursal->direccion }}</span>
        </div>
        @else
        <div class="info-row">
            <span class="info-label">Dirección:</span>
            <span>{{ $ordenEntrega->cliente->direccion }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Vendedor:</span>
            <span>{{ $ordenEntrega->vendedor->name }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Cantidad</th>
                <th style="width: 50%;">Descripción</th>
                <th style="width: 15%;" class="text-right">Precio Unit.</th>
                <th style="width: 10%;" class="text-right">ITBMS</th>
                <th style="width: 15%;" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ordenEntrega->detalles as $detalle)
            <tr>
                <td>{{ $detalle->cantidad }}</td>
                <td>{{ $detalle->producto->nombre }}</td>
                <td class="text-right">B/. {{ number_format($detalle->precio_unitario, 2) }}</td>
                <td class="text-right">B/. {{ number_format($detalle->itbms_monto, 2) }}</td>
                <td class="text-right">B/. {{ number_format($detalle->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span>B/. {{ number_format($ordenEntrega->subtotal, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">ITBMS (7%):</span>
            <span>B/. {{ number_format($ordenEntrega->itbms, 2) }}</span>
        </div>
        <div class="total-row grand-total">
            <span class="total-label">TOTAL:</span>
            <span>B/. {{ number_format($ordenEntrega->total, 2) }}</span>
        </div>
    </div>

    <div style="clear: both; margin-top: 100px;">
        @if($ordenEntrega->observaciones)
        <div style="margin-top: 20px;">
            <strong>Observaciones:</strong>
            <p>{{ $ordenEntrega->observaciones }}</p>
        </div>
        @endif

        <div style="margin-top: 50px;">
            <div style="display: inline-block; width: 45%; text-align: center; border-top: 1px solid #333;">
                Firma Vendedor
            </div>
            <div style="display: inline-block; width: 45%; margin-left: 9%; text-align: center; border-top: 1px solid #333;">
                Firma Cliente
            </div>
        </div>
    </div>

    <div class="footer">
        Documento generado el {{ now()->format('d/m/Y H:i:s') }}
    </div>
</body>
</html>
""")

print("\n✅ Vistas de Orden de Entrega actualizadas con:")
print("   - Campo de sucursal del cliente")
print("   - PDF de orden de entrega")
print("\nEjecuta:")
print("php artisan migrate")
print("python generate_orden_entrega_updated.py")