<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Location;
use App\Models\Shipment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Shipment> */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        $origin = Location::inRandomOrder()->first() ?? Location::factory()->create();
        $dest = Location::inRandomOrder()->first() ?? Location::factory()->create();
        $customer = Customer::inRandomOrder()->first() ?? Customer::factory()->create();
        return [
            'resi_no' => null, // will be set by observer
            'customer_id' => $customer->id,
            'sender_name' => fake()->name(),
            'sender_address' => fake()->address(),
            'receiver_name' => fake()->name(),
            'receiver_address' => fake()->address(),
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'service_type' => fake()->randomElement(['Express','Regular','Udara','Laut']),
            'payment_method' => fake()->randomElement(['Cash','COD','Transfer','Invoice']),
            'status' => 'Draft',
        ];
    }
}

