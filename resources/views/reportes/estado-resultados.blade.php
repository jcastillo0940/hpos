@extends('layouts.app')

@section('title', 'Estado de Resultados')
@section('page-title', 'Estado de Resultados')

@section('content')
<div class="max-w-5xl mx-auto">
    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold text-slate-800 mb-4">
            <i class="bi bi-funnel mr-2"></i>Período
        </h3>
        
        <form method="GET" action="{{ route('reportes.estado-resultados') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="bi bi-search mr-2"></i>Consultar
                </button>
            </div>
        </form>
    </div>

    <!-- Estado de Resultados -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-2xl font-bold text-slate-800">Estado de Resultados</h3>
            <p class="text-slate-600 mt-1">
                Del {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} 
                al {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
            </p>
        </div>

        <div class="p-6">
            <!-- Ingresos -->
            <div class="mb-6">
                <h4 class="text-lg font-bold text-slate-800 mb-3 border-b-2 border-blue-500 pb-2">
                    INGRESOS OPERACIONALES
                </h4>
                
                <div class="ml-4 space-y-2">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Ventas Brutas</span>
                        <span class="font-semibold text-green-600">B/. {{ number_format($ventas, 2) }}</span>
                    </div>
                    
                    @if($devoluciones > 0)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Menos: Devoluciones en Ventas</span>
                        <span class="font-semibold text-red-600">(B/. {{ number_format($devoluciones, 2) }})</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center py-3 border-t-2 border-gray-300 font-bold">
                        <span class="text-slate-800">VENTAS NETAS</span>
                        <span class="text-blue-700 text-lg">B/. {{ number_format($ventasNetas, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Costo de Ventas -->
            <div class="mb-6">
                <h4 class="text-lg font-bold text-slate-800 mb-3 border-b-2 border-amber-500 pb-2">
                    COSTO DE VENTAS
                </h4>
                
                <div class="ml-4 space-y-2">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Costo de Mercancías Vendidas</span>
                        <span class="font-semibold text-red-600">(B/. {{ number_format($costoVentas, 2) }})</span>
                    </div>
                    
                    @if($mermas > 0)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Pérdidas por Mermas y Productos Dañados</span>
                        <span class="font-semibold text-red-600">(B/. {{ number_format($mermas, 2) }})</span>
                    </div>
                    @endif
                    
                    <div class="flex justify-between items-center py-3 border-t-2 border-gray-300">
                        <span class="font-bold text-slate-800">TOTAL COSTO DE VENTAS</span>
                        <span class="font-bold text-red-600">(B/. {{ number_format($costoVentasTotal, 2) }})</span>
                    </div>
                </div>
            </div>

            <!-- Utilidad Bruta -->
            <div class="mb-6 bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-slate-800">UTILIDAD BRUTA</span>
                    <span class="text-2xl font-bold text-green-700">B/. {{ number_format($utilidadBruta, 2) }}</span>
                </div>
                @if($ventasNetas > 0)
                <div class="text-right mt-1">
                    <span class="text-sm text-green-600">
                        Margen: {{ number_format(($utilidadBruta / $ventasNetas) * 100, 2) }}%
                    </span>
                </div>
                @endif
            </div>

            <!-- Gastos Operacionales -->
            <div class="mb-6">
                <h4 class="text-lg font-bold text-slate-800 mb-3 border-b-2 border-purple-500 pb-2">
                    GASTOS OPERACIONALES
                </h4>
                
                <div class="ml-4 space-y-2">
                    @if($gastosVenta > 0)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Gastos de Venta</span>
                        <span class="font-semibold text-red-600">(B/. {{ number_format($gastosVenta, 2) }})</span>
                    </div>
                    @endif
                    
                    @if($gastosAdministracion > 0)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Gastos de Administración</span>
                        <span class="font-semibold text-red-600">(B/. {{ number_format($gastosAdministracion, 2) }})</span>
                    </div>
                    @endif
                    
                    @if($gastosOperacionales > 0)
                    <div class="flex justify-between items-center py-3 border-t-2 border-gray-300">
                        <span class="font-bold text-slate-800">Total Gastos Operacionales</span>
                        <span class="font-bold text-red-600">(B/. {{ number_format($gastosOperacionales, 2) }})</span>
                    </div>
                    @else
                    <div class="flex justify-between items-center py-2 text-slate-500">
                        <span>No hay gastos operacionales en este período</span>
                        <span>B/. 0.00</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Utilidad Operacional -->
            <div class="mb-6 bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-slate-800">UTILIDAD OPERACIONAL</span>
                    <span class="text-2xl font-bold text-blue-700">B/. {{ number_format($utilidadOperacional, 2) }}</span>
                </div>
            </div>

            <!-- Otros Ingresos y Gastos -->
            <div class="mb-6">
                <h4 class="text-lg font-bold text-slate-800 mb-3 border-b-2 border-indigo-500 pb-2">
                    OTROS INGRESOS Y GASTOS
                </h4>
                
                <div class="ml-4 space-y-2">
                    @if($ingresosFinancieros > 0)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Ingresos Financieros</span>
                        <span class="font-semibold text-green-600">B/. {{ number_format($ingresosFinancieros, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($gastosFinancieros > 0)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Gastos Financieros (Factoring)</span>
                        <span class="font-semibold text-red-600">(B/. {{ number_format($gastosFinancieros, 2) }})</span>
                    </div>
                    @endif
                    
                    @if($otrosIngresos > 0)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Otros Ingresos</span>
                        <span class="font-semibold text-green-600">B/. {{ number_format($otrosIngresos, 2) }}</span>
                    </div>
                    @endif
                    
                    @if($otrosGastos > 0)
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-700">Otros Gastos</span>
                        <span class="font-semibold text-red-600">(B/. {{ number_format($otrosGastos, 2) }})</span>
                    </div>
                    @endif
                    
                    @if($ingresosFinancieros == 0 && $gastosFinancieros == 0 && $otrosIngresos == 0 && $otrosGastos == 0)
                    <div class="flex justify-between items-center py-2 text-slate-500">
                        <span>No hay otros ingresos/gastos en este período</span>
                        <span>B/. 0.00</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Utilidad antes de Impuestos -->
            <div class="mb-6 bg-indigo-50 p-4 rounded-lg border-l-4 border-indigo-500">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-slate-800">UTILIDAD ANTES DE IMPUESTOS</span>
                    <span class="text-2xl font-bold text-indigo-700">B/. {{ number_format($utilidadAntesImpuestos, 2) }}</span>
                </div>
            </div>

            <!-- Impuesto sobre la Renta -->
            <div class="mb-6">
                <div class="ml-4 space-y-2">
                    <div class="flex justify-between items-center py-2">
                        <div>
                            <span class="text-slate-700">Impuesto sobre la Renta</span>
                            @if($utilidadAntesImpuestos > 0)
                            <span class="block text-xs text-slate-500 mt-1">
                                @if($utilidadAntesImpuestos <= 11000)
                                    Exento (hasta B/. 11,000)
                                @elseif($utilidadAntesImpuestos <= 50000)
                                    15% sobre exceso de B/. 11,000
                                @else
                                    15% hasta B/. 50,000 + 25% sobre exceso
                                @endif
                            </span>
                            @endif
                        </div>
                        <span class="font-semibold text-red-600">(B/. {{ number_format($impuestoRenta, 2) }})</span>
                    </div>
                </div>
            </div>

            <!-- Utilidad Neta -->
            <div class="bg-gradient-to-r from-green-100 to-emerald-100 p-6 rounded-lg border-2 border-green-500">
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-slate-800">UTILIDAD NETA</span>
                    <span class="text-4xl font-bold {{ $utilidadNeta >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        B/. {{ number_format($utilidadNeta, 2) }}
                    </span>
                </div>
                @if($ventasNetas > 0)
                <div class="text-right mt-2">
                    <span class="text-sm {{ $utilidadNeta >= 0 ? 'text-green-700' : 'text-red-700' }} font-semibold">
                        Margen Neto: {{ number_format(($utilidadNeta / $ventasNetas) * 100, 2) }}%
                    </span>
                </div>
                @endif
            </div>

            <!-- Notas Aclaratorias -->
            @if($devoluciones > 0 || $mermas > 0)
            <div class="mt-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                <h5 class="font-semibold text-slate-800 mb-2">
                    <i class="bi bi-info-circle mr-2"></i>Notas Aclaratorias
                </h5>
                <ul class="text-sm text-slate-600 space-y-1">
                    @if($devoluciones > 0)
                    <li>• Las devoluciones en ventas corresponden a productos devueltos por clientes</li>
                    @endif
                    @if($mermas > 0)
                    <li>• Las pérdidas por mermas incluyen productos dañados, vencidos y mermas de inventario</li>
                    @endif
                    @if($gastosFinancieros > 0)
                    <li>• Los gastos financieros incluyen descuentos por factoring y otros costos bancarios</li>
                    @endif
                </ul>
            </div>
            @endif
        </div>

        <!-- Botones de Acción -->
        <div class="p-6 border-t bg-gray-50 flex justify-end space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                <i class="bi bi-printer mr-2"></i>Imprimir
            </button>
            <button onclick="exportarPDF()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                <i class="bi bi-file-pdf mr-2"></i>Exportar PDF
            </button>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
}
</style>

@push('scripts')
<script>
function exportarPDF() {
    alert('Función de exportación PDF próximamente');
}
</script>
@endpush
@endsection