<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserTag;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserTag>
 */
final class UserTagFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->word(),
        ];
    }
}
