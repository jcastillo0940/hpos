<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\OrdenEntregaController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\NotaCreditoController;
use App\Http\Controllers\CobroController;
use App\Http\Controllers\RutaDiariaController;
use App\Http\Controllers\OrdenCompraController;
use App\Http\Controllers\RecepcionCompraController;
use App\Http\Controllers\FacturaCompraController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\ReporteController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Clientes
    Route::resource('clientes', ClienteController::class);
    Route::get('clientes/{cliente}/saldo', [ClienteController::class, 'saldo'])->name('clientes.saldo');
    
    // Productos
    Route::resource('productos', ProductoController::class);
    Route::get('productos/{producto}/stock', [ProductoController::class, 'stock'])->name('productos.stock');
    
    // Órdenes de Entrega
    Route::resource('ordenes-entrega', OrdenEntregaController::class);
    Route::post('ordenes-entrega/{ordenEntrega}/aprobar', [OrdenEntregaController::class, 'aprobar'])->name('ordenes-entrega.aprobar');
    Route::post('ordenes-entrega/{ordenEntrega}/anular', [OrdenEntregaController::class, 'anular'])->name('ordenes-entrega.anular');
    Route::post('ordenes-entrega/convertir-factura', [OrdenEntregaController::class, 'convertirFactura'])->name('ordenes-entrega.convertir-factura');
    
    // Facturas
    Route::resource('facturas', FacturaController::class);
    Route::get('facturas/{factura}/pdf', [FacturaController::class, 'pdf'])->name('facturas.pdf');
    Route::post('facturas/{factura}/anular', [FacturaController::class, 'anular'])->name('facturas.anular');
    
    // Notas de Crédito
    Route::resource('notas-credito', NotaCreditoController::class);
    Route::get('notas-credito/{notaCredito}/pdf', [NotaCreditoController::class, 'pdf'])->name('notas-credito.pdf');
    
    // Cobros
    Route::resource('cobros', CobroController::class);
    Route::post('cobros/{cobro}/aplicar', [CobroController::class, 'aplicar'])->name('cobros.aplicar');
    
    // Rutas Diarias
    Route::resource('rutas-diarias', RutaDiariaController::class);
    Route::post('rutas-diarias/{rutaDiaria}/iniciar', [RutaDiariaController::class, 'iniciar'])->name('rutas-diarias.iniciar');
    Route::post('rutas-diarias/{rutaDiaria}/finalizar', [RutaDiariaController::class, 'finalizar'])->name('rutas-diarias.finalizar');
    Route::post('rutas-diarias/{rutaDiaria}/liquidar', [RutaDiariaController::class, 'liquidar'])->name('rutas-diarias.liquidar');
    Route::post('rutas-diarias/{rutaDiaria}/entregar', [RutaDiariaController::class, 'registrarEntrega'])->name('rutas-diarias.entregar');
    
    // Órdenes de Compra
    Route::resource('ordenes-compra', OrdenCompraController::class);
    Route::post('ordenes-compra/{ordenCompra}/aprobar', [OrdenCompraController::class, 'aprobar'])->name('ordenes-compra.aprobar');
    Route::post('ordenes-compra/consolidar', [OrdenCompraController::class, 'consolidar'])->name('ordenes-compra.consolidar');
    
    // Recepciones
    Route::resource('recepciones-compra', RecepcionCompraController::class);
    Route::post('recepciones-compra/{recepcionCompra}/confirmar', [RecepcionCompraController::class, 'confirmar'])->name('recepciones-compra.confirmar');
    
    // Facturas de Compra
    Route::resource('facturas-compra', FacturaCompraController::class);
    
    // Pagos
    Route::resource('pagos', PagoController::class);
    Route::post('pagos/{pago}/aplicar', [PagoController::class, 'aplicar'])->name('pagos.aplicar');
    
    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('ventas', [ReporteController::class, 'ventas'])->name('ventas');
        Route::get('ventas-vendedor', [ReporteController::class, 'ventasVendedor'])->name('ventas-vendedor');
        Route::get('mermas', [ReporteController::class, 'mermas'])->name('mermas');
        Route::get('estado-resultados', [ReporteController::class, 'estadoResultados'])->name('estado-resultados');
        Route::get('balance', [ReporteController::class, 'balance'])->name('balance');
        Route::get('cuentas-cobrar', [ReporteController::class, 'cuentasCobrar'])->name('cuentas-cobrar');
        Route::get('cuentas-pagar', [ReporteController::class, 'cuentasPagar'])->name('cuentas-pagar');
        Route::get('inventario', [ReporteController::class, 'inventario'])->name('inventario');
    });
});

require __DIR__.'/auth.php';