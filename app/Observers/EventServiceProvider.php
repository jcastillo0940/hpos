<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Factura;
use App\Models\NotaCredito;
use App\Models\Cobro;
use App\Models\RutaDiaria;
use App\Observers\FacturaObserver;
use App\Observers\NotaCreditoObserver;
use App\Observers\CobroObserver;
use App\Observers\RutaDiariaObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];

    public function boot(): void
    {
        Factura::observe(FacturaObserver::class);
        NotaCredito::observe(NotaCreditoObserver::class);
        Cobro::observe(CobroObserver::class);
        RutaDiaria::observe(RutaDiariaObserver::class);
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}