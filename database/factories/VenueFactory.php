<?php

namespace Database\Factories;

use App\enums\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'post_code' => fake()->word(),
            'region' => $this->faker->randomElement(Region::class),
        ];
    }
}
