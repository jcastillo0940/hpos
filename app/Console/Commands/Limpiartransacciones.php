<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LimpiarTransacciones extends Command
{
    protected $signature = 'db:limpiar-transacciones {--force : Forzar limpieza sin confirmación}';
    
    protected $description = 'Elimina todas las transacciones de compra y venta (Órdenes, Facturas, Notas de Crédito, Pagos, Cobros)';

    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('??  ADVERTENCIA: Esto eliminará TODAS las transacciones (órdenes, facturas, notas, pagos, cobros). ¿Estás seguro?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        $this->info('?? Iniciando limpieza de transacciones...');
        
        DB::beginTransaction();
        
        try {
            // VENTAS
            $this->info('?? Limpiando Órdenes de Entrega...');
            DB::table('ordenes_entrega_detalle')->delete();
            $deleted = DB::table('ordenes_entrega')->delete();
            $this->line("   ? {$deleted} órdenes de entrega eliminadas");
            
            $this->info('?? Limpiando Facturas de Venta...');
            DB::table('facturas_detalle')->delete();
            $deleted = DB::table('facturas')->delete();
            $this->line("   ? {$deleted} facturas de venta eliminadas");
            
            $this->info('?? Limpiando Notas de Crédito...');
            DB::table('notas_credito_detalle')->delete();
            $deleted = DB::table('notas_credito')->delete();
            $this->line("   ? {$deleted} notas de crédito eliminadas");
            
            $this->info('?? Limpiando Cobros...');
            DB::table('cobros_detalle')->delete();
            $deleted = DB::table('cobros')->delete();
            $this->line("   ? {$deleted} cobros eliminados");
            
            // COMPRAS
            $this->info('?? Limpiando Órdenes de Compra...');
            DB::table('ordenes_compra_detalle')->delete();
            $deleted = DB::table('ordenes_compra')->delete();
            $this->line("   ? {$deleted} órdenes de compra eliminadas");
            
            $this->info('?? Limpiando Recepciones de Compra...');
            DB::table('recepciones_compra_detalle')->delete();
            $deleted = DB::table('recepciones_compra')->delete();
            $this->line("   ? {$deleted} recepciones de compra eliminadas");
            
            $this->info('?? Limpiando Facturas de Compra...');
            DB::table('facturas_compra_detalle')->delete();
            $deleted = DB::table('facturas_compra')->delete();
            $this->line("   ? {$deleted} facturas de compra eliminadas");
            
            $this->info('?? Limpiando Pagos...');
            DB::table('pagos_detalle')->delete();
            $deleted = DB::table('pagos')->delete();
            $this->line("   ? {$deleted} pagos eliminados");
            
            // RUTAS
            $this->info('?? Limpiando Rutas Diarias...');
            DB::table('rutas_diarias_detalle')->delete();
            $deleted = DB::table('rutas_diarias')->delete();
            $this->line("   ? {$deleted} rutas diarias eliminadas");
            
            // INVENTARIO
            $this->info('?? Reseteando Stocks...');
            $updated = DB::table('stocks')->update([
                'cantidad_disponible' => 0,
                'cantidad_reservada' => 0
            ]);
            $this->line("   ? {$updated} registros de stocks reseteados");
            
            // SALDOS
            $this->info('?? Reseteando Saldos de Clientes...');
            $updated = DB::table('clientes')->update(['saldo_actual' => 0]);
            $this->line("   ? {$updated} clientes reseteados");
            
            $this->info('?? Reseteando Saldos de Proveedores...');
            $updated = DB::table('proveedores')->update(['saldo_actual' => 0]);
            $this->line("   ? {$updated} proveedores reseteados");
            
            DB::commit();
            
            $this->newLine();
            $this->info('? Limpieza completada exitosamente!');
            $this->newLine();
            $this->warn('?? RESUMEN:');
            $this->line('   • Órdenes de Entrega eliminadas');
            $this->line('   • Facturas de Venta eliminadas');
            $this->line('   • Notas de Crédito eliminadas');
            $this->line('   • Cobros eliminados');
            $this->line('   • Órdenes de Compra eliminadas');
            $this->line('   • Recepciones de Compra eliminadas');
            $this->line('   • Facturas de Compra eliminadas');
            $this->line('   • Pagos eliminados');
            $this->line('   • Rutas Diarias eliminadas');
            $this->line('   • Stocks reseteado a 0');
            $this->line('   • Saldos de clientes y proveedores reseteados a 0');
            $this->newLine();
            $this->info('?? Los siguientes datos NO fueron afectados:');
            $this->line('   • Clientes');
            $this->line('   • Proveedores');
            $this->line('   • Productos');
            $this->line('   • Bodegas');
            $this->line('   • Listas de Precios');
            $this->line('   • Usuarios');
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('? Error durante la limpieza: ' . $e->getMessage());
            return 1;
        }
    }
}