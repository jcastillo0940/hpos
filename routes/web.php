<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProveedorController;
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
use App\Http\Controllers\ListaPrecioController;
use App\Http\Controllers\BodegaController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Clientes
    Route::resource('clientes', ClienteController::class);
    Route::get('clientes/{cliente}/saldo', [ClienteController::class, 'saldo'])->name('clientes.saldo');
    Route::get('clientes/{cliente}/sucursales', [OrdenEntregaController::class, 'getSucursales'])->name('clientes.sucursales');
    
    // Proveedores
    Route::resource('proveedores', ProveedorController::class)->parameters([
    'proveedores' => 'proveedor'
]);
    Route::get('proveedores/{proveedor}/saldo', [ProveedorController::class, 'saldo'])->name('proveedores.saldo');
    
    // Productos
    Route::resource('productos', ProductoController::class);
    Route::get('productos/{producto}/stock', [ProductoController::class, 'stock'])->name('productos.stock');
    
    // Bodegas
    Route::resource('bodegas', BodegaController::class);
    
    // Listas de Precios
    Route::resource('listas-precios', ListaPrecioController::class);
    Route::post('listas-precios/{lista}/productos', [ListaPrecioController::class, 'agregarProducto'])->name('listas-precios.agregar-producto');
    Route::put('listas-precios/{lista}/productos/{producto}', [ListaPrecioController::class, 'actualizarProducto'])->name('listas-precios.actualizar-producto');
    Route::delete('listas-precios/{lista}/productos/{producto}', [ListaPrecioController::class, 'eliminarProducto'])->name('listas-precios.eliminar-producto');
    Route::post('listas-precios/{lista}/recalcular', [ListaPrecioController::class, 'recalcularPrecios'])->name('listas-precios.recalcular');
    Route::post('listas-precios/{lista}/aplicar-global', [ListaPrecioController::class, 'aplicarGlobal'])->name('listas-precios.aplicar-global');
    
    // Órdenes de Entrega
    Route::resource('ordenes-entrega', OrdenEntregaController::class);
    Route::get('ordenes-entrega/{ordenEntrega}/pdf', [OrdenEntregaController::class, 'pdf'])->name('ordenes-entrega.pdf');
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
    Route::post('notas-credito/{notaCredito}/aplicar', [NotaCreditoController::class, 'aplicar'])->name('notas-credito.aplicar');
    Route::post('notas-credito/{notaCredito}/anular', [NotaCreditoController::class, 'anular'])->name('notas-credito.anular');
    
    // Cobros
    Route::resource('cobros', CobroController::class);
    Route::post('cobros/{cobro}/aplicar', [CobroController::class, 'aplicar'])->name('cobros.aplicar');
    Route::post('cobros/{cobro}/anular', [CobroController::class, 'anular'])->name('cobros.anular');
    
    // Rutas Diarias
    Route::resource('rutas-diarias', RutaDiariaController::class);
    Route::get('rutas-diarias/{rutaDiaria}/pdf', [RutaDiariaController::class, 'pdf'])->name('rutas-diarias.pdf');
    Route::post('rutas-diarias/{rutaDiaria}/iniciar', [RutaDiariaController::class, 'iniciar'])->name('rutas-diarias.iniciar');
    Route::post('rutas-diarias/{rutaDiaria}/finalizar', [RutaDiariaController::class, 'finalizar'])->name('rutas-diarias.finalizar');
    Route::post('rutas-diarias/{rutaDiaria}/liquidar', [RutaDiariaController::class, 'liquidar'])->name('rutas-diarias.liquidar');
    Route::post('rutas-diarias/{rutaDiaria}/entregar', [RutaDiariaController::class, 'registrarEntrega'])->name('rutas-diarias.entregar');
    
    // Órdenes de Compra
    Route::resource('ordenes-compra', OrdenCompraController::class)->parameters([
        'ordenes-compra' => 'ordenCompra'
    ]);
    Route::get('ordenes-compra/{ordenCompra}/pdf', [OrdenCompraController::class, 'pdf'])->name('ordenes-compra.pdf');
    Route::post('ordenes-compra/{ordenCompra}/aprobar', [OrdenCompraController::class, 'aprobar'])->name('ordenes-compra.aprobar');
    Route::post('ordenes-compra/consolidar', [OrdenCompraController::class, 'consolidar'])->name('ordenes-compra.consolidar');
    Route::get('api/ordenes-compra/{id}/detalles', [OrdenCompraController::class, 'detallesApi'])->name('api.ordenes-compra.detalles');
    
    // Recepciones de Compra
    Route::resource('recepciones-compra', RecepcionCompraController::class)->parameters([
        'recepciones-compra' => 'recepcionCompra'
    ]);
    Route::post('recepciones-compra/{recepcionCompra}/confirmar', [RecepcionCompraController::class, 'confirmar'])->name('recepciones-compra.confirmar');
    
    // Facturas de Compra
    Route::resource('facturas-compra', FacturaCompraController::class)->parameters([
        'facturas-compra' => 'facturaCompra'
    ]);
    
    // Pagos
    Route::resource('pagos', PagoController::class);
    Route::post('pagos/{pago}/aplicar', [PagoController::class, 'aplicar'])->name('pagos.aplicar');
    Route::post('pagos/{pago}/anular', [PagoController::class, 'anular'])->name('pagos.anular');
    Route::get('api/facturas-pendientes/{proveedorId}', [PagoController::class, 'getFacturasPendientes'])->name('api.facturas-pendientes');
    
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
        Route::get('estado-cuenta', [ReporteController::class, 'estadoCuenta'])->name('estado-cuenta');
        Route::get('estado-cuenta-pdf', [ReporteController::class, 'estadoCuentaPdf'])->name('estado-cuenta-pdf');
    });
});

require __DIR__.'/auth.php';