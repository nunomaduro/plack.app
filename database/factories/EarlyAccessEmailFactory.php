<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EarlyAccessEmail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EarlyAccessEmail>
 */
final class EarlyAccessEmailFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
