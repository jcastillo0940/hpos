<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\NotaCredito;
use App\Models\Cobro;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\AsientoContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function ventas(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth());
        
        $ventas = Factura::empresaActual()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'anulada')
            ->with(['cliente', 'vendedor'])
            ->get();
        
        $totalVentas = $ventas->sum('total');
        $totalITBMS = $ventas->sum('itbms');
        
        return view('reportes.ventas', compact('ventas', 'totalVentas', 'totalITBMS', 'fechaInicio', 'fechaFin'));
    }

    public function ventasVendedor(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth());
        
        $ventasPorVendedor = Factura::empresaActual()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'anulada')
            ->select('vendedor_id', DB::raw('SUM(total) as total_ventas'), DB::raw('COUNT(*) as cantidad_facturas'))
            ->groupBy('vendedor_id')
            ->with('vendedor')
            ->get();
        
        return view('reportes.ventas-vendedor', compact('ventasPorVendedor', 'fechaInicio', 'fechaFin'));
    }

    public function mermas(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth());
        
        $mermas = NotaCredito::empresaActual()
            ->where('tipo', 'devolucion')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['cliente', 'detalles.producto'])
            ->get();
        
        $totalMermas = $mermas->sum('total');
        
        return view('reportes.mermas', compact('mermas', 'totalMermas', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Estado de Resultados con ISR Progresivo de Panamá
     */
    public function estadoResultados(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        
        // Ventas (subtotal sin ITBMS)
        $ventas = Factura::empresaActual()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'anulada')
            ->sum('subtotal');
        
        // Devoluciones en ventas (solo las que son devolución real del cliente)
        // Excluir: producto_dañado, producto_vencido, merma (esos van al costo)
        $devoluciones = NotaCredito::empresaActual()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'aplicada')
            ->where('tipo', 'devolucion')
            ->whereNotIn('motivo', ['producto_dañado', 'producto_vencido', 'merma'])
            ->sum('subtotal');
        
        $ventasNetas = $ventas - $devoluciones;
        
        // Costo de Ventas - desde asientos contables
        $costoVentas = DB::table('asientos_contables')
            ->join('asientos_contables_detalle', 'asientos_contables.id', '=', 'asientos_contables_detalle.asiento_contable_id')
            ->join('plan_cuentas', 'asientos_contables_detalle.cuenta_id', '=', 'plan_cuentas.id')
            ->where('asientos_contables.empresa_id', auth()->user()->empresa_id)
            ->whereBetween('asientos_contables.fecha', [$fechaInicio, $fechaFin])
            ->where('asientos_contables.estado', 'contabilizado')
            ->where('plan_cuentas.cuenta_sistema', 'costo_ventas')
            ->sum('asientos_contables_detalle.debito');
        
        // Pérdidas por Mermas y Productos Dañados/Vencidos
        $mermas = NotaCredito::empresaActual()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'aplicada')
            ->where('tipo', 'devolucion')
            ->whereIn('motivo', ['producto_dañado', 'producto_vencido', 'merma'])
            ->sum('subtotal');
        
        $costoVentasTotal = $costoVentas + $mermas;
        
        $utilidadBruta = $ventasNetas - $costoVentasTotal;
        
        // Gastos Operacionales - Gastos de Administración (6.2-6.7 excepto gastos financieros)
        $gastosAdministracion = DB::table('asientos_contables')
            ->join('asientos_contables_detalle', 'asientos_contables.id', '=', 'asientos_contables_detalle.asiento_contable_id')
            ->join('plan_cuentas', 'asientos_contables_detalle.cuenta_id', '=', 'plan_cuentas.id')
            ->where('asientos_contables.empresa_id', auth()->user()->empresa_id)
            ->whereBetween('asientos_contables.fecha', [$fechaInicio, $fechaFin])
            ->where('asientos_contables.estado', 'contabilizado')
            ->where('plan_cuentas.tipo', 'egreso')
            ->where(function($q) {
                $q->where('plan_cuentas.codigo', 'LIKE', '6.2%')
                  ->orWhere('plan_cuentas.codigo', 'LIKE', '6.3%')
                  ->orWhere('plan_cuentas.codigo', 'LIKE', '6.4%')
                  ->orWhere('plan_cuentas.codigo', 'LIKE', '6.5%')
                  ->orWhere('plan_cuentas.codigo', 'LIKE', '6.6%')
                  ->orWhere('plan_cuentas.codigo', 'LIKE', '6.7%');
            })
            ->where('plan_cuentas.codigo', '!=', '6.82')
            ->whereNull('plan_cuentas.cuenta_sistema')
            ->sum('asientos_contables_detalle.debito');
        
        // Gastos de Venta (6.1)
        $gastosVenta = DB::table('asientos_contables')
            ->join('asientos_contables_detalle', 'asientos_contables.id', '=', 'asientos_contables_detalle.asiento_contable_id')
            ->join('plan_cuentas', 'asientos_contables_detalle.cuenta_id', '=', 'plan_cuentas.id')
            ->where('asientos_contables.empresa_id', auth()->user()->empresa_id)
            ->whereBetween('asientos_contables.fecha', [$fechaInicio, $fechaFin])
            ->where('asientos_contables.estado', 'contabilizado')
            ->where('plan_cuentas.tipo', 'egreso')
            ->where('plan_cuentas.codigo', 'LIKE', '6.1%')
            ->sum('asientos_contables_detalle.debito');
        
        $gastosOperacionales = $gastosAdministracion + $gastosVenta;
        $utilidadOperacional = $utilidadBruta - $gastosOperacionales;
        
        // Ingresos Financieros
        $ingresosFinancieros = DB::table('asientos_contables')
            ->join('asientos_contables_detalle', 'asientos_contables.id', '=', 'asientos_contables_detalle.asiento_contable_id')
            ->join('plan_cuentas', 'asientos_contables_detalle.cuenta_id', '=', 'plan_cuentas.id')
            ->where('asientos_contables.empresa_id', auth()->user()->empresa_id)
            ->whereBetween('asientos_contables.fecha', [$fechaInicio, $fechaFin])
            ->where('asientos_contables.estado', 'contabilizado')
            ->where('plan_cuentas.tipo', 'ingreso')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '4.2%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '4.3%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '4.4%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '4.5%')
            ->sum('asientos_contables_detalle.credito');
        
        // Gastos Financieros (cuenta_sistema = gastos_financieros)
        $gastosFinancieros = DB::table('asientos_contables')
            ->join('asientos_contables_detalle', 'asientos_contables.id', '=', 'asientos_contables_detalle.asiento_contable_id')
            ->join('plan_cuentas', 'asientos_contables_detalle.cuenta_id', '=', 'plan_cuentas.id')
            ->where('asientos_contables.empresa_id', auth()->user()->empresa_id)
            ->whereBetween('asientos_contables.fecha', [$fechaInicio, $fechaFin])
            ->where('asientos_contables.estado', 'contabilizado')
            ->where('plan_cuentas.cuenta_sistema', 'gastos_financieros')
            ->sum('asientos_contables_detalle.debito');
        
        // Otros Ingresos
        $otrosIngresos = 0;
        
        // Otros Gastos (gastos no operacionales excepto financieros)
        $otrosGastos = DB::table('asientos_contables')
            ->join('asientos_contables_detalle', 'asientos_contables.id', '=', 'asientos_contables_detalle.asiento_contable_id')
            ->join('plan_cuentas', 'asientos_contables_detalle.cuenta_id', '=', 'plan_cuentas.id')
            ->where('asientos_contables.empresa_id', auth()->user()->empresa_id)
            ->whereBetween('asientos_contables.fecha', [$fechaInicio, $fechaFin])
            ->where('asientos_contables.estado', 'contabilizado')
            ->where('plan_cuentas.tipo', 'egreso')
            ->where('plan_cuentas.codigo', 'LIKE', '6.%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '6.1%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '6.2%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '6.3%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '6.4%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '6.5%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '6.6%')
            ->where('plan_cuentas.codigo', 'NOT LIKE', '6.7%')
            ->where('plan_cuentas.codigo', '!=', '6.82')
            ->whereNull('plan_cuentas.cuenta_sistema')
            ->sum('asientos_contables_detalle.debito');
        
        $utilidadAntesImpuestos = $utilidadOperacional + $ingresosFinancieros - $gastosFinancieros + $otrosIngresos - $otrosGastos;
        
        // Impuesto sobre la renta en Panamá (progresivo)
        $impuestoRenta = 0;
        if ($utilidadAntesImpuestos > 11000) {
            if ($utilidadAntesImpuestos <= 50000) {
                // 15% sobre el exceso de B/. 11,000
                $impuestoRenta = ($utilidadAntesImpuestos - 11000) * 0.15;
            } else {
                // 15% sobre B/. 39,000 + 25% sobre el exceso de B/. 50,000
                $impuestoRenta = (39000 * 0.15) + (($utilidadAntesImpuestos - 50000) * 0.25);
            }
        }
        
        $utilidadNeta = $utilidadAntesImpuestos - $impuestoRenta;
        
        return view('reportes.estado-resultados', compact(
            'ventas', 'devoluciones', 'ventasNetas', 'costoVentas', 'mermas', 'costoVentasTotal',
            'utilidadBruta', 'gastosAdministracion', 'gastosVenta', 
            'gastosOperacionales', 'utilidadOperacional',
            'ingresosFinancieros', 'gastosFinancieros', 'otrosIngresos', 'otrosGastos',
            'utilidadAntesImpuestos', 'impuestoRenta', 'utilidadNeta',
            'fechaInicio', 'fechaFin'
        ));
    }

    public function cuentasCobrar()
    {
        $clientes = Cliente::empresaActual()
            ->where('saldo_actual', '>', 0)
            ->with(['facturas' => function($q) {
                $q->where('saldo_pendiente', '>', 0);
            }])
            ->get();
        
        $totalCxC = $clientes->sum('saldo_actual');
        $totalVencido = $clientes->sum('saldo_vencido');
        
        return view('reportes.cuentas-cobrar', compact('clientes', 'totalCxC', 'totalVencido'));
    }

    public function inventario()
    {
        $productos = Producto::empresaActual()
            ->with('stocks.bodega')
            ->get()
            ->map(function($producto) {
                $stockTotal = $producto->stocks->sum('cantidad_disponible');
                $valorInventario = $stockTotal * $producto->costo;
                
                return [
                    'producto' => $producto,
                    'stock_total' => $stockTotal,
                    'valor_inventario' => $valorInventario,
                ];
            });
        
        $valorTotalInventario = $productos->sum('valor_inventario');
        
        return view('reportes.inventario', compact('productos', 'valorTotalInventario'));
    }

    /**
     * Estado de Cuenta del Cliente
     */
    public function estadoCuenta(Request $request)
    {
        $clienteId = $request->input('cliente_id');
        $año = $request->input('año', date('Y'));
        $mes = $request->input('mes', date('m'));
        
        // Obtener todos los clientes para el select
        $clientes = Cliente::empresaActual()
            ->where('activo', true)
            ->orderBy('nombre_comercial')
            ->get();
        
        if (!$clienteId) {
            $cliente = null;
            return view('reportes.estado-cuenta', compact('clientes', 'año', 'mes', 'cliente'));
        }
        
        $cliente = Cliente::findOrFail($clienteId);
        
        // Si mes = 0, mostrar resumen anual
        if ($mes == 0) {
            return $this->estadoCuentaAnual($cliente, $año, $clientes);
        }
        
        // Estado de cuenta mensual
        return $this->estadoCuentaMensual($cliente, $año, $mes, $clientes);
    }

    /**
     * Estado de Cuenta Mensual
     */
    private function estadoCuentaMensual($cliente, $año, $mes, $clientes)
    {
        $fechaInicio = Carbon::create($año, $mes, 1)->startOfMonth();
        $fechaFin = Carbon::create($año, $mes, 1)->endOfMonth();
        
        // Calcular saldo anterior (mes anterior)
        $mesAnterior = $mes == 1 ? 12 : $mes - 1;
        $añoAnterior = $mes == 1 ? $año - 1 : $año;
        $fechaFinMesAnterior = Carbon::create($añoAnterior, $mesAnterior, 1)->endOfMonth();
        
        $saldoAnterior = $this->calcularSaldoHasta($cliente->id, $fechaFinMesAnterior);
        
        // Obtener movimientos del mes
        $movimientos = collect();
        
        // Facturas
        $facturas = Factura::where('cliente_id', $cliente->id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'anulada')
            ->get()
            ->map(function($f) {
                return (object)[
                    'fecha' => $f->fecha,
                    'tipo' => 'factura',
                    'numero' => $f->numero,
                    'descripcion' => 'Factura de venta',
                    'cargo' => $f->total,
                    'abono' => 0,
                ];
            });
        
        // Cobros aplicados
        $cobros = Cobro::whereHas('detalles', function($q) use ($cliente) {
                $q->whereHas('factura', function($q2) use ($cliente) {
                    $q2->where('cliente_id', $cliente->id);
                });
            })
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'aplicado')
            ->with('detalles')
            ->get()
            ->map(function($c) use ($cliente) {
                $montoAplicado = $c->detalles->sum(function($d) use ($cliente) {
                    return $d->factura->cliente_id == $cliente->id ? $d->monto_aplicado : 0;
                });
                
                return (object)[
                    'fecha' => $c->fecha,
                    'tipo' => 'cobro',
                    'numero' => $c->numero,
                    'descripcion' => 'Pago - ' . ucfirst($c->tipo_pago),
                    'cargo' => 0,
                    'abono' => $montoAplicado,
                ];
            });
        
        // Notas de crédito
        $notasCredito = NotaCredito::where('cliente_id', $cliente->id)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'aplicada')
            ->get()
            ->map(function($nc) {
                return (object)[
                    'fecha' => $nc->fecha,
                    'tipo' => 'nota_credito',
                    'numero' => $nc->numero,
                    'descripcion' => 'Nota de Crédito - ' . $nc->motivo,
                    'cargo' => 0,
                    'abono' => $nc->total,
                ];
            });
        
        // Combinar y ordenar todos los movimientos
        $movimientos = $facturas->concat($cobros)->concat($notasCredito)->sortBy('fecha')->values();
        
        // Calcular totales
        $totalCargos = $movimientos->sum('cargo');
        $totalAbonos = $movimientos->sum('abono');
        $saldoFinal = $saldoAnterior + $totalCargos - $totalAbonos;
        
        // Nombres de meses
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $nombreMes = $meses[$mes];
        $mesAnteriorNombre = $meses[$mesAnterior];
        
        return view('reportes.estado-cuenta', compact(
            'cliente', 'clientes', 'año', 'mes', 'nombreMes', 
            'saldoAnterior', 'movimientos', 'totalCargos', 'totalAbonos', 
            'saldoFinal', 'mesAnteriorNombre', 'añoAnterior'
        ));
    }

    /**
     * Estado de Cuenta Anual (Resumen)
     */
    private function estadoCuentaAnual($cliente, $año, $clientes)
    {
        $mes = 0;
        $resumenAnual = [];
        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        
        $saldoAcumulado = $this->calcularSaldoHasta($cliente->id, Carbon::create($año, 1, 1)->subDay());
        
        for ($m = 1; $m <= 12; $m++) {
            $fechaInicio = Carbon::create($año, $m, 1)->startOfMonth();
            $fechaFin = Carbon::create($año, $m, 1)->endOfMonth();
            
            $facturas = Factura::where('cliente_id', $cliente->id)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->where('estado', '!=', 'anulada')
                ->sum('total');
            
            $cobros = Cobro::whereHas('detalles', function($q) use ($cliente) {
                    $q->whereHas('factura', function($q2) use ($cliente) {
                        $q2->where('cliente_id', $cliente->id);
                    });
                })
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->where('estado', 'aplicado')
                ->with('detalles')
                ->get()
                ->sum(function($c) use ($cliente) {
                    return $c->detalles->sum(function($d) use ($cliente) {
                        return $d->factura->cliente_id == $cliente->id ? $d->monto_aplicado : 0;
                    });
                });
            
            $notasCredito = NotaCredito::where('cliente_id', $cliente->id)
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->where('estado', 'aplicada')
                ->sum('total');
            
            $saldoFinalMes = $saldoAcumulado + $facturas - $cobros - $notasCredito;
            
            $resumenAnual[] = [
                'mes' => $m,
                'mes_nombre' => $meses[$m - 1],
                'saldo_inicial' => $saldoAcumulado,
                'facturas' => $facturas,
                'cobros' => $cobros,
                'notas_credito' => $notasCredito,
                'saldo_final' => $saldoFinalMes,
            ];
            
            $saldoAcumulado = $saldoFinalMes;
        }
        
        $totalesAnuales = [
            'facturas' => collect($resumenAnual)->sum('facturas'),
            'cobros' => collect($resumenAnual)->sum('cobros'),
            'notas_credito' => collect($resumenAnual)->sum('notas_credito'),
        ];
        
        return view('reportes.estado-cuenta', compact('cliente', 'clientes', 'año', 'mes', 'resumenAnual', 'totalesAnuales'));
    }

    /**
     * Calcular saldo hasta una fecha determinada
     */
    private function calcularSaldoHasta($clienteId, $fecha)
    {
        $facturas = Factura::where('cliente_id', $clienteId)
            ->where('fecha', '<=', $fecha)
            ->where('estado', '!=', 'anulada')
            ->sum('total');
        
        $cobros = Cobro::whereHas('detalles', function($q) use ($clienteId) {
                $q->whereHas('factura', function($q2) use ($clienteId) {
                    $q2->where('cliente_id', $clienteId);
                });
            })
            ->where('fecha', '<=', $fecha)
            ->where('estado', 'aplicado')
            ->with('detalles')
            ->get()
            ->sum(function($c) use ($clienteId) {
                return $c->detalles->sum(function($d) use ($clienteId) {
                    return $d->factura->cliente_id == $clienteId ? $d->monto_aplicado : 0;
                });
            });
        
        $notasCredito = NotaCredito::where('cliente_id', $clienteId)
            ->where('fecha', '<=', $fecha)
            ->where('estado', 'aplicada')
            ->sum('total');
        
        return $facturas - $cobros - $notasCredito;
    }

    /**
     * Generar PDF del Estado de Cuenta
     */
    public function estadoCuentaPdf(Request $request)
    {
        $clienteId = $request->input('cliente_id');
        $año = $request->input('año', date('Y'));
        $mes = $request->input('mes', date('m'));
        
        if (!$clienteId) {
            return redirect()->route('reportes.estado-cuenta')
                ->with('error', 'Debe seleccionar un cliente');
        }
        
        $cliente = Cliente::with('empresa')->findOrFail($clienteId);
        
        // Calcular datos del estado de cuenta
        $fechaInicio = Carbon::create($año, $mes, 1)->startOfMonth();
        $fechaFin = Carbon::create($año, $mes, 1)->endOfMonth();
        
        // Calcular saldo anterior
        $mesAnterior = $mes == 1 ? 12 : $mes - 1;
        $añoAnterior = $mes == 1 ? $año - 1 : $año;
        $fechaFinMesAnterior = Carbon::create($añoAnterior, $mesAnterior, 1)->endOfMonth();
        
        $saldoAnterior = $this->calcularSaldoHasta($cliente->id, $fechaFinMesAnterior);
        
        // Obtener movimientos
        $movimientos = $this->obtenerMovimientos($cliente->id, $fechaInicio, $fechaFin);
        
        // Calcular totales
        $totalCargos = $movimientos->sum('cargo');
        $totalAbonos = $movimientos->sum('abono');
        $saldoFinal = $saldoAnterior + $totalCargos - $totalAbonos;
        
        // Nombres de meses
        $meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $nombreMes = $meses[$mes];
        $mesAnteriorNombre = $meses[$mesAnterior];
        $nombrePeriodo = $nombreMes . ' ' . $año;
        
        $pdf = \PDF::loadView('reportes.estado-cuenta-pdf', compact(
            'cliente', 'movimientos', 'saldoAnterior', 'totalCargos', 
            'totalAbonos', 'saldoFinal', 'nombreMes', 'mesAnteriorNombre', 
            'añoAnterior', 'nombrePeriodo'
        ));
        
        return $pdf->stream("estado-cuenta-{$cliente->codigo}-{$año}-{$mes}.pdf");
    }

    /**
     * Obtener movimientos del cliente en un período
     */
    private function obtenerMovimientos($clienteId, $fechaInicio, $fechaFin)
    {
        $movimientos = collect();
        
        // Facturas
        $facturas = Factura::where('cliente_id', $clienteId)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'anulada')
            ->get()
            ->map(function($f) {
                return (object)[
                    'fecha' => $f->fecha,
                    'tipo' => 'factura',
                    'numero' => $f->numero,
                    'descripcion' => 'Factura de venta',
                    'cargo' => $f->total,
                    'abono' => 0,
                ];
            });
        
        // Cobros
        $cobros = Cobro::whereHas('detalles', function($q) use ($clienteId) {

                $q->whereHas('factura', function($q2) use ($clienteId) {
                    $q2->where('cliente_id', $clienteId);
                });
            })
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'aplicado')
            ->with('detalles')
            ->get()
            ->map(function($c) use ($clienteId) {
                $montoAplicado = $c->detalles->sum(function($d) use ($clienteId) {
                    return $d->factura->cliente_id == $clienteId ? $d->monto_aplicado : 0;
                });
                
                return (object)[
                    'fecha' => $c->fecha,
                    'tipo' => 'cobro',
                    'numero' => $c->numero,
                    'descripcion' => 'Pago - ' . ucfirst($c->tipo_pago),
                    'cargo' => 0,
                    'abono' => $montoAplicado,
                ];
            });
        
        // Notas de crédito
        $notasCredito = NotaCredito::where('cliente_id', $clienteId)
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', 'aplicada')
            ->get()
            ->map(function($nc) {
                return (object)[
                    'fecha' => $nc->fecha,
                    'tipo' => 'nota_credito',
                    'numero' => $nc->numero,
                    'descripcion' => 'Nota de Crédito - ' . $nc->tipo,
                    'cargo' => 0,
                    'abono' => $nc->total,
                ];
            });
        
        return $facturas->concat($cobros)->concat($notasCredito)->sortBy('fecha')->values();
    }
}