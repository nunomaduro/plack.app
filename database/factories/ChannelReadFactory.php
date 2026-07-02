<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\ChannelRead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChannelRead>
 */
final class ChannelReadFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'channel_id' => Channel::factory(),
            'last_read_at' => now(),
        ];
    }
}
