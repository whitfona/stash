<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Living Room', 'Guest Bedroom', 'Garage', 'Kitchen', 'Attic', 'Basement', 'Closet']),
            'notes' => fake()->optional()->sentence(),
            'parent_id' => null,
        ];
    }
}
