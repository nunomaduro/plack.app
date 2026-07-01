<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ChannelVisibility;
use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Channel>
 */
final class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => $this->faker->name(),
            'visibility' => ChannelVisibility::Public,
        ];
    }

    public function public(): self
    {
        return $this->state([
            'visibility' => ChannelVisibility::Public,
        ]);
    }

    public function private(): self
    {
        return $this->state([
            'visibility' => ChannelVisibility::Private,
        ]);
    }
}
