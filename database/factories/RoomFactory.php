<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Room>
 */
final class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'      => $this->faker->unique()->bothify('????-####'),
            'icon'      => $this->faker->randomElement(['lucide:book-open', 'lucide:film', 'lucide:rocket', 'lucide:fish', 'lucide:code']),
            'password'  => $this->faker->optional()->password(),
            'theme'     => $this->faker->randomElement(['space', 'films', 'programming']),
            'type'      => $this->faker->randomElement(['private', 'public']),
            'max_users' => $this->faker->numberBetween(2, 10),
            'game_mode' => $this->faker->randomElement(['default', 'hard_potato']),
            'status'    => $this->faker->randomElement(['waiting', 'playing', 'finished']),
            'user_id'   => User::factory(),
        ];
    }
}
