<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\OrdenEntrega;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $empresaId = auth()->user()->empresa_id;
        
        $ventasHoy = Factura::where('empresa_id', $empresaId)
            ->whereDate('fecha', today())
            ->where('estado', '!=', 'anulada')
            ->sum('total');
        
        $ventasMes = Factura::where('empresa_id', $empresaId)
            ->whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->where('estado', '!=', 'anulada')
            ->sum('total');
        
        $cxcVencidas = Cliente::where('empresa_id', $empresaId)
            ->sum('saldo_vencido');
        
        $ordenesPendientes = OrdenEntrega::where('empresa_id', $empresaId)
            ->where('estado', 'pendiente')
            ->count();
        
        $productosStockBajo = Producto::where('empresa_id', $empresaId)
            ->whereRaw('(SELECT SUM(cantidad_disponible) FROM stocks WHERE stocks.producto_id = productos.id) < productos.stock_minimo')
            ->count();
        
        return view('dashboard', compact(
            'ventasHoy',
            'ventasMes',
            'cxcVencidas',
            'ordenesPendientes',
            'productosStockBajo'
        ));
    }
}