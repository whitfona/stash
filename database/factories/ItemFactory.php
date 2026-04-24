<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'aliases' => fake()->optional()->randomElements(['alias one', 'alias two', 'alias three'], 2),
            'description' => fake()->optional()->sentence(),
            'tags' => fake()->optional()->randomElements(['seasonal', 'holiday', 'tools', 'clothes', 'decor'], 2),
            'location_id' => \App\Models\Location::factory(),
        ];
    }
}
