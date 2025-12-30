@extends('layouts.app')

@section('title', 'Órdenes de Entrega')
@section('page-title', 'Órdenes de Entrega')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-slate-800">Órdenes de Entrega</h3>
        <p class="text-slate-600 mt-1">Gestiona las órdenes de entrega a clientes</p>
    </div>
    @can('crear_ordenes_entrega')
    <a href="{{ route('ordenes-entrega.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
        <i class="bi bi-plus-circle mr-2"></i>
        Nueva Orden de Entrega
    </a>
    @endcan
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Pendientes</p>
                <p class="text-2xl font-bold text-amber-600">{{ $ordenesEntrega->where('estado', 'pendiente')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-hourglass-split text-amber-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Aprobadas</p>
                <p class="text-2xl font-bold text-blue-600">{{ $ordenesEntrega->where('estado', 'aprobada')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-check-circle text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Facturadas</p>
                <p class="text-2xl font-bold text-green-600">{{ $ordenesEntrega->where('estado', 'facturada')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-receipt text-green-600 text-xl"></i>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-slate-600">Total Monto</p>
                <p class="text-2xl font-bold text-slate-800">B/. {{ number_format($ordenesEntrega->sum('total'), 2) }}</p>
            </div>
            <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-currency-dollar text-slate-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2">
            <input type="text" name="search" placeholder="Buscar por número o cliente..." value="{{ request('search') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
        <div>
            <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Todos los estados</option>
                <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobada" {{ request('estado') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                <option value="facturada" {{ request('estado') == 'facturada' ? 'selected' : '' }}>Facturada</option>
                <option value="anulada" {{ request('estado') == 'anulada' ? 'selected' : '' }}>Anulada</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-medium rounded-lg transition">
            <i class="bi bi-search mr-2"></i>Buscar
        </button>
    </form>
</div>

<form method="POST" action="{{ route('ordenes-entrega.convertir-factura') }}" id="formConversion">
    @csrf
    <input type="hidden" name="ordenes" id="ordenesInput">
    
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="checkAll" class="w-4 h-4 text-blue-600 rounded">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ordenesEntrega as $orden)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            @if($orden->estado == 'aprobada')
                            <input type="checkbox" class="orden-checkbox w-4 h-4 text-blue-600 rounded" value="{{ $orden->id }}" data-numero="{{ $orden->numero }}">
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono font-medium text-blue-600">{{ $orden->numero }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            {{ $orden->fecha ? $orden->fecha->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <p class="font-medium text-slate-800">{{ $orden->cliente->nombre_comercial ?? 'N/A' }}</p>
                                @if($orden->clienteSucursal)
                                <p class="text-xs text-slate-500"><i class="bi bi-building mr-1"></i>{{ $orden->clienteSucursal->nombre }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                            {{ $orden->vendedor->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-bold text-slate-800">B/. {{ number_format($orden->total, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($orden->estado)
                                @case('pendiente')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <i class="bi bi-hourglass-split mr-1"></i> Pendiente
                                    </span>
                                    @break
                                @case('aprobada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="bi bi-check-circle mr-1"></i> Aprobada
                                    </span>
                                    @break
                                @case('facturada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="bi bi-receipt mr-1"></i> Facturada
                                    </span>
                                    @break
                                @case('anulada')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="bi bi-x-circle mr-1"></i> Anulada
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('ordenes-entrega.show', $orden) }}" class="text-blue-600 hover:text-blue-900 transition" title="Ver detalles">
                                    <i class="bi bi-eye text-lg"></i>
                                </a>
                                
                                @if($orden->estado == 'pendiente')
                                    @can('convertir_ordenes_entrega')
                                    <form method="POST" action="{{ route('ordenes-entrega.aprobar', $orden) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 transition" title="Aprobar">
                                            <i class="bi bi-check-circle text-lg"></i>
                                        </button>
                                    </form>
                                    @endcan
                                    
                                    <form method="POST" action="{{ route('ordenes-entrega.anular', $orden) }}" class="inline" onsubmit="return confirm('¿Anular esta orden?')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition" title="Anular">
                                            <i class="bi bi-x-circle text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-slate-400">
                                <i class="bi bi-inbox text-5xl mb-3"></i>
                                <p class="text-lg font-medium">No hay órdenes de entrega</p>
                                <p class="text-sm">Las órdenes aparecerán aquí cuando las crees</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ordenesEntrega->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $ordenesEntrega->links() }}
        </div>
        @endif

        <div id="barraConversion" class="border-t border-gray-200 px-6 py-4 bg-blue-50" style="display: none;">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-blue-900">
                    <i class="bi bi-check-square mr-2"></i>
                    <span id="contadorSeleccionadas">0</span> orden(es) seleccionada(s) para convertir a factura
                </p>
                <button type="button" onclick="convertirAFactura()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition shadow-sm hover:shadow-md">
                    <i class="bi bi-receipt mr-2"></i>Convertir a Factura
                </button>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
// Sistema de logs persistentes
const Logger = {
    logs: [],
    
    init() {
        // Cargar logs previos
        const savedLogs = localStorage.getItem('conversion_logs');
        if (savedLogs) {
            this.logs = JSON.parse(savedLogs);
        }
    },
    
    log(type, message, data = null) {
        const entry = {
            timestamp: new Date().toISOString(),
            type: type,
            message: message,
            data: data,
            url: window.location.href
        };
        
        this.logs.push(entry);
        localStorage.setItem('conversion_logs', JSON.stringify(this.logs));
        
        // También mostrar en consola
        const emoji = {
            'info': '📄',
            'success': '✅',
            'error': '❌',
            'debug': '🔍',
            'action': '🎯',
            'send': '📤'
        }[type] || '📋';
        
        console.log(`${emoji} [${type.toUpperCase()}] ${message}`, data || '');
    },
    
    clear() {
        this.logs = [];
        localStorage.removeItem('conversion_logs');
        console.log('🗑️ Logs limpiados');
    },
    
    show() {
        console.log('📊 === LOGS COMPLETOS ===');
        this.logs.forEach((log, index) => {
            console.log(`\n[${index + 1}] ${log.timestamp}`);
            console.log(`Tipo: ${log.type}`);
            console.log(`Mensaje: ${log.message}`);
            console.log(`URL: ${log.url}`);
            if (log.data) {
                console.log('Datos:', log.data);
            }
        });
        console.log('\n📊 === FIN LOGS ===\n');
        return this.logs;
    },
    
    download() {
        const blob = new Blob([JSON.stringify(this.logs, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `conversion_logs_${new Date().getTime()}.json`;
        a.click();
        console.log('💾 Logs descargados');
    }
};

// Inicializar logger
Logger.init();
Logger.log('info', 'Script de conversión cargado');

document.addEventListener('DOMContentLoaded', function() {
    Logger.log('info', 'DOM cargado completamente');
    
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.orden-checkbox');
    const barraConversion = document.getElementById('barraConversion');
    const contadorSeleccionadas = document.getElementById('contadorSeleccionadas');
    const formConversion = document.getElementById('formConversion');
    const ordenesInput = document.getElementById('ordenesInput');
    
    Logger.log('debug', 'Elementos encontrados', {
        checkAll: !!checkAll,
        checkboxesCount: checkboxes.length,
        barraConversion: !!barraConversion,
        formConversion: !!formConversion,
        ordenesInput: !!ordenesInput
    });
    
    // Listar órdenes disponibles
    const ordenesDisponibles = Array.from(checkboxes).map(cb => ({
        id: cb.value,
        numero: cb.dataset.numero
    }));
    Logger.log('info', 'Órdenes aprobadas disponibles', ordenesDisponibles);
    
    // Marcar/desmarcar todos
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            Logger.log('action', 'Check All clickeado', { checked: this.checked });
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            actualizarBarra();
        });
    }
    
    // Escuchar cambios en cada checkbox
    checkboxes.forEach((cb, index) => {
        cb.addEventListener('change', function() {
            Logger.log('action', `Checkbox ${index} cambió`, {
                id: this.value,
                numero: this.dataset.numero,
                checked: this.checked
            });
            actualizarBarra();
        });
    });
    
    // Actualizar barra de conversión
    function actualizarBarra() {
        const seleccionados = Array.from(checkboxes).filter(cb => cb.checked);
        const cantidad = seleccionados.length;
        
        const ordenesSeleccionadas = seleccionados.map(cb => ({
            id: cb.value,
            numero: cb.dataset.numero
        }));
        
        Logger.log('debug', 'Actualizar barra', {
            cantidad: cantidad,
            ordenes: ordenesSeleccionadas
        });
        
        if (cantidad > 0) {
            barraConversion.style.display = 'block';
            contadorSeleccionadas.textContent = cantidad;
        } else {
            barraConversion.style.display = 'none';
        }
    }
});

// Convertir a factura
function convertirAFactura() {
    Logger.log('action', 'Función convertirAFactura() ejecutada');
    
    const checkboxes = document.querySelectorAll('.orden-checkbox:checked');
    Logger.log('debug', 'Checkboxes marcados encontrados', { count: checkboxes.length });
    
    if (checkboxes.length === 0) {
        Logger.log('error', 'No hay órdenes seleccionadas');
        alert('Debe seleccionar al menos una orden');
        return;
    }
    
    const ordenesIds = Array.from(checkboxes).map(cb => parseInt(cb.value));
    const ordenesNumeros = Array.from(checkboxes).map(cb => cb.dataset.numero);
    const ordenesJSON = JSON.stringify(ordenesIds);
    
    Logger.log('send', 'Preparando datos para envío', {
        ids: ordenesIds,
        numeros: ordenesNumeros,
        json: ordenesJSON
    });
    
    const ordenesInput = document.getElementById('ordenesInput');
    ordenesInput.value = ordenesJSON;
    
    Logger.log('debug', 'Input hidden actualizado', { value: ordenesInput.value });
    
    const formConversion = document.getElementById('formConversion');
    Logger.log('send', 'Enviando formulario', {
        action: formConversion.action,
        method: formConversion.method,
        formData: new FormData(formConversion)
    });
    
    // Guardar en localStorage que se está enviando
    localStorage.setItem('conversion_enviando', JSON.stringify({
        timestamp: new Date().toISOString(),
        ordenesIds: ordenesIds,
        ordenesNumeros: ordenesNumeros
    }));
    
    Logger.log('success', '🚀 FORMULARIO ENVIADO - La página se recargará');
    
    formConversion.submit();
}

// Verificar si venimos de un envío
const conversionEnviando = localStorage.getItem('conversion_enviando');
if (conversionEnviando) {
    const data = JSON.parse(conversionEnviando);
    Logger.log('info', '✅ PÁGINA RECARGADA después de envío', data);
    localStorage.removeItem('conversion_enviando');
    
    // Verificar la URL actual
    const currentUrl = window.location.href;
    Logger.log('info', 'URL después de recarga', { url: currentUrl });
}

// Funciones globales para debug
window.showLogs = function() {
    return Logger.show();
}

window.clearLogs = function() {
    Logger.clear();
}

window.downloadLogs = function() {
    Logger.download();
}

window.debugConversion = function() {
    const checkboxes = document.querySelectorAll('.orden-checkbox');
    const checked = document.querySelectorAll('.orden-checkbox:checked');
    
    const info = {
        total: checkboxes.length,
        marcados: checked.length,
        ids: Array.from(checked).map(cb => cb.value),
        inputValue: document.getElementById('ordenesInput').value
    };
    
    Logger.log('debug', 'Debug manual ejecutado', info);
    console.log('🔍 [DEBUG MANUAL]', info);
    return info;
}

console.log('💡 Comandos disponibles:');
console.log('   showLogs()      - Mostrar todos los logs');
console.log('   clearLogs()     - Limpiar logs');
console.log('   downloadLogs()  - Descargar logs como JSON');
console.log('   debugConversion() - Ver estado actual');
</script>
@endpush
@endsection