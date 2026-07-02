<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\ChannelRead;
use App\Models\User;

final readonly class MarkChannelAsRead
{
    public function handle(Channel $channel, User $user): void
    {
        ChannelRead::query()->updateOrCreate(
            ['user_id' => $user->id, 'channel_id' => $channel->id],
            ['last_read_at' => now()],
        );
    }
}
