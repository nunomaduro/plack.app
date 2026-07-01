<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\Channel;
use App\Models\User;

final readonly class IsChannelMember
{
    public function get(User $user, Channel $channel): bool
    {
        return $channel->members()
            ->whereKey($user->id)
            ->exists();
    }
}
