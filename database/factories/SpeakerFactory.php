<?php

namespace Database\Factories;

use App\Models\Speaker;
use App\Models\Talk;
use Illuminate\Database\Eloquent\Factories\Factory;

class SpeakerFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $qualificationsCount = $this->faker->numberBetween(1, 10);
        $qualifications = $this->faker->randomElements(array_keys(Speaker::QUALIFICATIONS), $qualificationsCount);

        return [
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'bio' => fake()->text(),
            'twitter_handle' => fake()->word(),
            'qualifications' => $qualifications,
        ];
    }

    public function withTalks(int $count = 1): self
    {
        return $this->has(Talk::factory()->count($count), 'talks');
    }
}
