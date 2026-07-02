<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\WorkspaceType;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Workspace>
 */
final class WorkspaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->company(),
            'type' => WorkspaceType::Private,
            'join_code' => null,
        ];
    }

    public function private(): self
    {
        return $this->state(fn (): array => [
            'type' => WorkspaceType::Private,
            'join_code' => null,
        ]);
    }

    public function public(): self
    {
        return $this->state(fn (): array => [
            'type' => WorkspaceType::Public,
            'join_code' => Str::random(64),
        ]);
    }
}
