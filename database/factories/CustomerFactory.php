<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Customer> */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->bothify('CUST-####')),
            'name' => fake()->company(),
            'npwp' => fake()->numerify('##.###.###.#-###.###'),
            'payment_term_days' => fake()->randomElement([0,14,30]),
            'credit_limit' => fake()->randomElement([null, 50000000, 100000000]),
            'notes' => fake()->sentence(),
        ];
    }
}

