<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\NotaCredito;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\AsientoContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            ->where('tipo', 'merma')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['cliente', 'detalles.producto'])
            ->get();
        
        $totalMermas = $mermas->sum('total');
        
        return view('reportes.mermas', compact('mermas', 'totalMermas', 'fechaInicio', 'fechaFin'));
    }

    public function estadoResultados(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth());
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth());
        
        $ventas = Factura::empresaActual()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'anulada')
            ->sum('subtotal');
        
        $devoluciones = NotaCredito::empresaActual()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->sum('subtotal');
        
        $costoVentas = AsientoContable::empresaActual()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->whereHas('detalles', function($q) {
                $q->whereHas('cuenta', function($q2) {
                    $q2->where('cuenta_sistema', 'costo_ventas');
                });
            })
            ->get()
            ->sum(function($asiento) {
                return $asiento->detalles->where('cuenta.cuenta_sistema', 'costo_ventas')->sum('debito');
            });
        
        $ventasNetas = $ventas - $devoluciones;
        $utilidadBruta = $ventasNetas - $costoVentas;
        
        return view('reportes.estado-resultados', compact(
            'ventas', 'devoluciones', 'ventasNetas', 'costoVentas', 
            'utilidadBruta', 'fechaInicio', 'fechaFin'
        ));
    }

    public function cuentasCobrar()
    {
        $clientes = Cliente::empresaActual()
            ->where('saldo_actual', '>', 0)
            ->with(['facturas' => function($q) {
                $q->whereIn('estado', ['pendiente', 'parcial', 'vencida']);
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
                $valorInventario = $stockTotal * $producto->costo_unitario;
                
                return [
                    'producto' => $producto,
                    'stock_total' => $stockTotal,
                    'valor_inventario' => $valorInventario,
                ];
            });
        
        $valorTotalInventario = $productos->sum('valor_inventario');
        
        return view('reportes.inventario', compact('productos', 'valorTotalInventario'));
    }
}