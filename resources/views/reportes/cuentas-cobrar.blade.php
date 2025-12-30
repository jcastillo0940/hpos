@extends('layouts.app')

@section('title', 'Cuentas por Cobrar')
@section('page-title', 'Cuentas por Cobrar')

@section('content')
<div class="mb-6">
    <h3 class="text-2xl font-bold text-slate-800">Reporte de Cuentas por Cobrar</h3>
    <p class="text-slate-600 mt-1">Estado de las cuentas pendientes de los clientes</p>
</div>

<!-- Resumen -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600 mb-1">Total por Cobrar</p>
                <p class="text-3xl font-bold text-blue-600">B/. {{ number_format($totalCxC, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="bi bi-wallet2 text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600 mb-1">Clientes con Saldo</p>
                <p class="text-3xl font-bold text-slate-800">{{ $clientes->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="bi bi-people text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600 mb-1">Saldo Vencido</p>
                <p class="text-3xl font-bold text-red-600">B/. {{ number_format($totalVencido, 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <i class="bi bi-exclamation-triangle text-2xl text-red-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de Clientes -->
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h4 class="text-lg font-semibold text-slate-800">Detalle por Cliente</h4>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">RUC/Cédula</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teléfono</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Días Crédito</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Actual</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo Vencido</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($clientes as $cliente)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-medium text-slate-800">{{ $cliente->razon_social }}</p>
                        @if($cliente->nombre_comercial && $cliente->nombre_comercial != $cliente->razon_social)
                            <p class="text-xs text-slate-500">{{ $cliente->nombre_comercial }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $cliente->ruc ?? $cliente->cedula ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $cliente->telefono ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-600">
                        {{ $cliente->dias_credito }} días
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-blue-600">B/. {{ number_format($cliente->saldo_actual, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($cliente->saldo_vencido > 0)
                            <span class="font-bold text-red-600">B/. {{ number_format($cliente->saldo_vencido, 2) }}</span>
                        @else
                            <span class="text-slate-400">B/. 0.00</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('reportes.estado-cuenta', ['cliente_id' => $cliente->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            <i class="bi bi-file-text mr-1"></i>Ver Estado de Cuenta
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <i class="bi bi-check-circle text-5xl mb-3"></i>
                            <p class="text-lg font-medium">No hay cuentas por cobrar</p>
                            <p class="text-sm">Todos los clientes están al día</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($clientes->count() > 0)
            <tfoot class="bg-gray-50 font-bold">
                <tr>
                    <td colspan="4" class="px-6 py-4 text-right">TOTALES:</td>
                    <td class="px-6 py-4">
                        <span class="text-blue-600">B/. {{ number_format($totalCxC, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-red-600">B/. {{ number_format($totalVencido, 2) }}</span>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

<!-- Botones de Exportación -->
<div class="mt-6 flex justify-end space-x-4">
    <button onclick="window.print()" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition">
        <i class="bi bi-printer mr-2"></i>Imprimir
    </button>
    <button onclick="exportarExcel()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
        <i class="bi bi-file-excel mr-2"></i>Exportar Excel
    </button>
</div>

@push('scripts')
<script>
function exportarExcel() {
    // Simple CSV export
    let csv = 'Cliente,RUC/Cédula,Teléfono,Días Crédito,Saldo Actual,Saldo Vencido\n';
    
    @foreach($clientes as $cliente)
    csv += '"{{ $cliente->razon_social }}",';
    csv += '"{{ $cliente->ruc ?? $cliente->cedula ?? 'N/A' }}",';
    csv += '"{{ $cliente->telefono ?? 'N/A' }}",';
    csv += '"{{ $cliente->dias_credito }}",';
    csv += '"{{ number_format($cliente->saldo_actual, 2) }}",';
    csv += '"{{ number_format($cliente->saldo_vencido, 2) }}"\n';
    @endforeach
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'cuentas-por-cobrar-' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
}
</script>
@endpush

@endsection
