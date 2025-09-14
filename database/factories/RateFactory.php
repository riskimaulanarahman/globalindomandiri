<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Rate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Rate> */
class RateFactory extends Factory
{
    protected $model = Rate::class;

    public function definition(): array
    {
        $origin = Location::inRandomOrder()->first() ?? Location::factory()->create();
        $dest = Location::inRandomOrder()->first() ?? Location::factory()->create();
        while ($dest->id === $origin->id) {
            $dest = Location::factory()->create();
        }
        $type = fake()->randomElement(['Express','Regular','Udara','Laut']);
        return [
            'origin_id' => $origin->id,
            'destination_id' => $dest->id,
            'service_type' => $type,
            'price' => fake()->randomFloat(2, 5000, 25000),
            'lead_time' => fake()->randomElement(['1-2 DAYS','2-4 DAYS','3-5 DAYS']),
            'is_active' => true,
        ];
    }
}

