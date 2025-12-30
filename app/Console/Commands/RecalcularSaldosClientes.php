<?php

namespace App\Console\Commands;

use App\Models\Cliente;
use Illuminate\Console\Command;

class RecalcularSaldosClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientes:recalcular-saldos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula los saldos actuales y vencidos de todos los clientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Recalculando saldos de clientes...');
        $this->newLine();
        
        $clientes = Cliente::all();
        
        if ($clientes->isEmpty()) {
            $this->warn('No hay clientes para procesar.');
            return 0;
        }
        
        $bar = $this->output->createProgressBar($clientes->count());
        $bar->start();
        
        foreach ($clientes as $cliente) {
            $cliente->actualizarSaldos();
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info('✅ Saldos recalculados exitosamente para ' . $clientes->count() . ' clientes');
        
        // Mostrar resumen
        $totalSaldoActual = Cliente::sum('saldo_actual');
        $totalSaldoVencido = Cliente::sum('saldo_vencido');
        
        $this->newLine();
        $this->table(
            ['Concepto', 'Monto'],
            [
                ['Total Saldo Actual', 'B/. ' . number_format($totalSaldoActual, 2)],
                ['Total Saldo Vencido', 'B/. ' . number_format($totalSaldoVencido, 2)],
            ]
        );
        
        return 0;
    }
}