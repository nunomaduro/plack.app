<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\StatusPreset;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StatusPreset>
 */
final class StatusPresetFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'emoji' => fake()->randomElement(['😀', '🏠', '🏖️', '🤒', '📅']),
            'text' => fake()->sentence(3),
        ];
    }
}
