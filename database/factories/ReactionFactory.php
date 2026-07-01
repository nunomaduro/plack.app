<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Emoji;
use App\Models\Channel;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reaction>
 */
final class ReactionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $channel = Channel::factory()->create();

        return [
            'user_id' => User::factory(),
            'emoji' => $this->faker->randomElement(Emoji::cases())->value,
            'reactable_type' => $channel->getMorphClass(),
            'reactable_id' => $channel->id,
        ];
    }
}
