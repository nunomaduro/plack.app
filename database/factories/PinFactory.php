<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pin>
 */
final class PinFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $channel = Channel::factory()->create();

        return [
            'channel_id' => $channel->id,
            'user_id' => User::factory(),
            'message_id' => Message::factory()->for($channel),
        ];
    }
}
