<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\ChannelMemberRole;
use App\Models\Channel;
use App\Models\User;

final readonly class IsChannelAdmin
{
    public function get(User $user, Channel $channel): bool
    {
        return $channel->members()
            ->wherePivot('role', ChannelMemberRole::Admin->value)
            ->whereKey($user->id)
            ->exists();
    }
}
