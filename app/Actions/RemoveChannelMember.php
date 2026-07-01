<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\User;

final readonly class RemoveChannelMember
{
    public function handle(Channel $channel, User $user): void
    {
        $channel->members()->detach($user->getKey());
    }
}
