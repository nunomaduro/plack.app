<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\User;
use App\Models\UserChannelMetadata;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserChannelMetadata>
 */
final class UserChannelMetadataFactory extends Factory
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
            'channel_id' => Channel::factory(),
            'last_read_at' => now(),
            'muted_at' => null,
        ];
    }
}
