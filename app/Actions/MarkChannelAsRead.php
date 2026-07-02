<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\User;
use App\Models\UserChannelMetadata;

final readonly class MarkChannelAsRead
{
    /**
     * Record that the user has read the channel up to now. The metadata row
     * is created the first time the user visits the channel.
     */
    public function handle(Channel $channel, User $user): UserChannelMetadata
    {
        return $channel->userChannelMetadata()->updateOrCreate(
            ['user_id' => $user->id],
            ['last_read_at' => now()],
        );
    }
}
