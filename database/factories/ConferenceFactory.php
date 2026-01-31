<?php

namespace Database\Factories;

use App\enums\Region;
use App\enums\Status;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConferenceFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $startDate = now()->addMonths(2);
        $endDate = now()->addMonths(2)->addDays(2);

        return [
            'name' => fake()->name(),
            'description' => fake()->text(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => fake()->randomElement(Status::class),
            'region' => fake()->randomElement(Region::class),
            'venue_id' => null,
        ];
    }
}
