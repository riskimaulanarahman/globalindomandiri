<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Shipment::observe(\App\Observers\ShipmentNumberObserver::class);
        $auditObserver = new \App\Observers\AuditObserver();
        foreach ([
            \App\Models\Customer::class,
            \App\Models\Location::class,
            \App\Models\Rate::class,
            \App\Models\Shipment::class,
            \App\Models\ShipmentItem::class,
            \App\Models\Invoice::class,
            \App\Models\InvoiceLine::class,
            \App\Models\Payment::class,
            \App\Models\Driver::class,
            \App\Models\Vehicle::class,
            \App\Models\Forwarder::class,
            \App\Models\ShipmentAssignment::class,
        ] as $model) {
            $model::observe($auditObserver);
        }
    }
}
