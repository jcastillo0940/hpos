import os

def create_file(path, content):
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"✓ Created: {path}")

# Vista principal de Estado de Cuenta
create_file("resources/views/reportes/estado-cuenta.blade.php", """@extends('layouts.app')

@section('title', 'Estado de Cuenta del Cliente')
@section('page-title', 'Estado de Cuenta del Cliente')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">
            <i class="bi bi-funnel mr-2"></i>Filtros
        </h3>
        
        <form method="GET" action="{{ route('reportes.estado-cuenta') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Cliente *</label>
                <select name="cliente_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Seleccionar cliente...</option>
                    @foreach($clientes as $c)
                    <option value="{{ $c->id }}" {{ $cliente && $cliente->id == $c->id ? 'selected' : '' }}>
                        {{ $c->codigo }} - {{ $c->nombre_comercial }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Año</label>
                <select name="año" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ $año == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Mes</label>
                <select name="mes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="0" {{ $mes == 0 ? 'selected' : '' }}>Todo el año</option>
                    @foreach(['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'] as $i => $m)
                    <option value="{{ $i + 1 }}" {{ $mes == ($i + 1) ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-search mr-2"></i>Consultar
                </button>
            </div>
        </form>
    </div>

    @if($cliente)
    <!-- Información del Cliente -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">{{ $cliente->nombre_comercial }}</h3>
                <p class="text-slate-600">RUC: {{ $cliente->identificacion }}</p>
                <p class="text-sm text-slate-500">{{ $cliente->direccion ?? 'Sin dirección' }}</p>
            </div>
            <div class="text-right">
                <a href="{{ route('reportes.estado-cuenta-pdf', ['cliente_id' => $cliente->id, 'año' => $año, 'mes' => $mes]) }}" 
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition mb-2">
                    <i class="bi bi-file-pdf mr-2"></i>Descargar PDF
                </a>
                <br>
                <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    <i class="bi bi-printer mr-2"></i>Imprimir
                </button>
            </div>
        </div>

        <div class="grid grid-cols-4 gap-4">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-600 font-medium">Límite de Crédito</p>
                <p class="text-2xl font-bold text-blue-700">B/. {{ number_format($cliente->limite_credito, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-amber-50 rounded-lg">
                <p class="text-sm text-amber-600 font-medium">Saldo Actual</p>
                <p class="text-2xl font-bold text-amber-700">B/. {{ number_format($cliente->saldo_actual, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <p class="text-sm text-red-600 font-medium">Saldo Vencido</p>
                <p class="text-2xl font-bold text-red-700">B/. {{ number_format($cliente->saldo_vencido, 2) }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-green-600 font-medium">Crédito Disponible</p>
                <p class="text-2xl font-bold text-green-700">B/. {{ number_format($cliente->limite_credito - $cliente->saldo_actual, 2) }}</p>
            </div>
        </div>
    </div>

    @if($mes > 0)
    <!-- Estado de Cuenta Mensual -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-slate-800">
                Estado de Cuenta - {{ $nombreMes }} {{ $año }}
            </h3>
        </div>

        <div class="p-6">
            <!-- Saldo Anterior -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg border-l-4 border-gray-400">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-slate-700">Saldo Anterior ({{ $mesAnteriorNombre }} {{ $añoAnterior }})</span>
                    <span class="text-xl font-bold text-gray-700">B/. {{ number_format($saldoAnterior, 2) }}</span>
                </div>
            </div>

            <!-- Movimientos del Mes -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cargos</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Abonos</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $saldoAcumulado = $saldoAnterior; @endphp
                        
                        @forelse($movimientos as $mov)
                        @php
                            $saldoAcumulado += $mov->cargo - $mov->abono;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $mov->fecha->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($mov->tipo === 'factura')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                    <i class="bi bi-receipt mr-1"></i> Factura
                                </span>
                                @elseif($mov->tipo === 'cobro')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <i class="bi bi-cash-coin mr-1"></i> Cobro
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="bi bi-arrow-return-left mr-1"></i> N/C
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-mono text-sm">{{ $mov->numero }}</td>
                            <td class="px-4 py-3 text-sm">{{ $mov->descripcion }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-red-600">
                                {{ $mov->cargo > 0 ? 'B/. ' . number_format($mov->cargo, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-green-600">
                                {{ $mov->abono > 0 ? 'B/. ' . number_format($mov->abono, 2) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-700">
                                B/. {{ number_format($saldoAcumulado, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                No hay movimientos en este período
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-right">TOTALES DEL MES:</td>
                            <td class="px-4 py-3 text-right text-red-700">B/. {{ number_format($totalCargos, 2) }}</td>
                            <td class="px-4 py-3 text-right text-green-700">B/. {{ number_format($totalAbonos, 2) }}</td>
                            <td class="px-4 py-3 text-right text-blue-700">B/. {{ number_format($saldoFinal, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @else
    <!-- Resumen Anual -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden mb-6">
        <div class="p-6 border-b">
            <h3 class="text-xl font-bold text-slate-800">Resumen Anual - {{ $año }}</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mes</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Inicial</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Facturas</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cobros</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">N/C</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo Final</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Ver</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($resumenAnual as $resumen)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">{{ $resumen['mes_nombre'] }}</td>
                        <td class="px-6 py-4 text-right">B/. {{ number_format($resumen['saldo_inicial'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-red-600 font-semibold">B/. {{ number_format($resumen['facturas'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-green-600 font-semibold">B/. {{ number_format($resumen['cobros'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-blue-600 font-semibold">B/. {{ number_format($resumen['notas_credito'], 2) }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-700">B/. {{ number_format($resumen['saldo_final'], 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('reportes.estado-cuenta', ['cliente_id' => $cliente->id, 'año' => $año, 'mes' => $resumen['mes']]) }}" 
                               class="text-blue-600 hover:text-blue-900">
                                <i class="bi bi-eye text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td class="px-6 py-4">TOTALES {{ $año }}</td>
                        <td class="px-6 py-4 text-right">-</td>
                        <td class="px-6 py-4 text-right text-red-700">B/. {{ number_format($totalesAnuales['facturas'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-green-700">B/. {{ number_format($totalesAnuales['cobros'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-blue-700">B/. {{ number_format($totalesAnuales['notas_credito'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-blue-700">B/. {{ number_format($cliente->saldo_actual, 2) }}</td>
                        <td class="px-6 py-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif
    @endif
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
}
</style>
@endsection
""")

print("✅ Vista de Estado de Cuenta creada")
print("\nAhora crea el controlador con:")
print("Ejecuta: python generate_estado_cuenta_complete.py")