<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Location;
use App\Models\Payment;
use App\Models\Rate;
use App\Models\Shipment;
use App\Policies\ModelPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Customer::class => ModelPolicy::class,
        Location::class => ModelPolicy::class,
        Rate::class => ModelPolicy::class,
        Shipment::class => ModelPolicy::class,
        Invoice::class => ModelPolicy::class,
        Payment::class => ModelPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}

