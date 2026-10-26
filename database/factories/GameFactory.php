<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'player_one_id' => User::factory(), // Assuming you have a User factory to create users
            'player_two_id' => null, // You can set this to a random user ID or leave it null for now
            'state' => null, // You can set this to a default state or leave it null for now
        ];
    }
}
