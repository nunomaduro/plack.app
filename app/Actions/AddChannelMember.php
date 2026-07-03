<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ChannelMemberRole;
use App\Models\Channel;
use App\Models\User;

final readonly class AddChannelMember
{
    public function handle(Channel $channel, User $user, ChannelMemberRole $role = ChannelMemberRole::Member): void
    {
        $channel->members()->syncWithoutDetaching([
            $user->id => ['role' => $role->value],
        ]);
    }
}
