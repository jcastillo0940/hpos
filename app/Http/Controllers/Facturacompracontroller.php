<?php

namespace App\Http\Controllers;

use App\Models\FacturaCompra;
use App\Models\FacturaCompraDetalle;
use App\Models\OrdenCompra;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class FacturaCompraController extends Controller
{
    public function index(Request $request)
    {
        $query = FacturaCompra::with(['proveedor', 'ordenCompra'])
            ->where('empresa_id', Auth::user()->empresa_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_factura', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', function($q2) use ($search) {
                      $q2->where('razon_social', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        $facturasCompra = $query->orderBy('fecha', 'desc')->paginate(15);
        $proveedores = Proveedor::where('empresa_id', Auth::user()->empresa_id)
            ->where('activo', true)
            ->get();

        return view('facturas-compra.index', compact('facturasCompra', 'proveedores'));
    }

    public function create()
    {
        $proveedores = Proveedor::where('empresa_id', Auth::user()->empresa_id)
            ->where('activo', true)
            ->get();
        
        $ordenesCompra = OrdenCompra::where('empresa_id', Auth::user()->empresa_id)
            ->whereIn('estado', ['aprobada', 'recibida'])
            ->with('proveedor')
            ->get();

        return view('facturas-compra.create', compact('proveedores', 'ordenesCompra'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'numero_factura' => 'required|string|max:100',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha',
            'proveedor_id' => 'required|exists:proveedores,id',
            'orden_compra_id' => 'nullable|exists:ordenes_compra,id',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $subtotal = 0;
            $itbms = 0;

            foreach ($validated['detalles'] as $detalle) {
                $producto = \App\Models\Producto::find($detalle['producto_id']);
                $subtotalLinea = $detalle['cantidad'] * $detalle['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);
                
                $subtotal += $subtotalLinea;
                $itbms += $itbmsLinea;
            }

            $total = $subtotal + $itbms;

            $facturaCompra = FacturaCompra::create([
                'empresa_id' => Auth::user()->empresa_id,
                'numero_factura' => $validated['numero_factura'],
                'fecha' => $validated['fecha'],
                'fecha_vencimiento' => $validated['fecha_vencimiento'],
                'proveedor_id' => $validated['proveedor_id'],
                'orden_compra_id' => $validated['orden_compra_id'] ?? null,
                'subtotal' => $subtotal,
                'itbms' => $itbms,
                'total' => $total,
                'saldo_pendiente' => $total,
                'estado' => 'pendiente',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            foreach ($validated['detalles'] as $detalle) {
                $producto = \App\Models\Producto::find($detalle['producto_id']);
                $subtotalLinea = $detalle['cantidad'] * $detalle['precio_unitario'];
                $itbmsLinea = $subtotalLinea * ($producto->itbms / 100);

                FacturaCompraDetalle::create([
                    'factura_compra_id' => $facturaCompra->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                    'itbms_porcentaje' => $producto->itbms,
                    'itbms_monto' => $itbmsLinea,
                    'subtotal' => $subtotalLinea,
                    'total' => $subtotalLinea + $itbmsLinea,
                ]);
            }

            // Actualizar orden de compra si existe
            if ($validated['orden_compra_id']) {
                $ordenCompra = OrdenCompra::find($validated['orden_compra_id']);
                $ordenCompra->update(['estado' => 'facturada']);
            }

            // Actualizar saldo del proveedor
            $proveedor = Proveedor::find($validated['proveedor_id']);
            $proveedor->increment('saldo_actual', $total);

            return redirect()->route('facturas-compra.show', $facturaCompra)
                ->with('success', 'Factura de compra creada exitosamente');
        });
    }

    public function show($id)
    {
        $facturaCompra = FacturaCompra::with(['proveedor', 'ordenCompra', 'detalles.producto', 'pagos'])
            ->findOrFail($id);

        return view('facturas-compra.show', compact('facturaCompra'));
    }

    public function edit(FacturaCompra $facturaCompra)
    {
        if ($facturaCompra->estado !== 'pendiente') {
            return redirect()->route('facturas-compra.show', $facturaCompra)
                ->with('error', 'No se puede editar una factura que no está en estado pendiente');
        }

        $proveedores = Proveedor::where('empresa_id', Auth::user()->empresa_id)
            ->where('activo', true)
            ->get();

        return view('facturas-compra.edit', compact('facturaCompra', 'proveedores'));
    }

    public function update(Request $request, FacturaCompra $facturaCompra)
    {
        if ($facturaCompra->estado !== 'pendiente') {
            return redirect()->route('facturas-compra.show', $facturaCompra)
                ->with('error', 'No se puede editar una factura que no está en estado pendiente');
        }

        $validated = $request->validate([
            'numero_factura' => 'required|string|max:100',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'required|date|after_or_equal:fecha',
            'observaciones' => 'nullable|string',
        ]);

        $facturaCompra->update($validated);

        return redirect()->route('facturas-compra.show', $facturaCompra)
            ->with('success', 'Factura de compra actualizada exitosamente');
    }

    public function destroy(FacturaCompra $facturaCompra)
    {
        if ($facturaCompra->estado !== 'pendiente') {
            return redirect()->route('facturas-compra.index')
                ->with('error', 'No se puede eliminar una factura que no está en estado pendiente');
        }

        DB::transaction(function () use ($facturaCompra) {
            // Actualizar saldo del proveedor
            $proveedor = $facturaCompra->proveedor;
            $proveedor->decrement('saldo_actual', $facturaCompra->saldo_pendiente);

            // Actualizar orden de compra si existe
            if ($facturaCompra->orden_compra_id) {
                $ordenCompra = $facturaCompra->ordenCompra;
                $ordenCompra->update(['estado' => 'recibida']);
            }

            $facturaCompra->delete();
        });

        return redirect()->route('facturas-compra.index')
            ->with('success', 'Factura de compra eliminada exitosamente');
    }
}