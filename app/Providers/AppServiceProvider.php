<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Factura;
use App\Models\Cobro;
use App\Models\NotaCredito;
use App\Observers\FacturaObserver;
use App\Observers\CobroObserver;
use App\Observers\NotaCreditoObserver;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Registrar Observers para actualización automática de saldos
        Factura::observe(FacturaObserver::class);
        Cobro::observe(CobroObserver::class);
        NotaCredito::observe(NotaCreditoObserver::class);
    }
}