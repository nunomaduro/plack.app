<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Channel;
use App\Models\User;
use App\Queries\IsChannelAdmin;
use App\Queries\IsChannelMember;

final readonly class ChannelPolicy
{
    public function __construct(
        private IsChannelMember $isChannelMember,
        private IsChannelAdmin $isChannelAdmin,
    ) {}

    /**
     * Determine whether the user can view the channel.
     */
    public function view(User $user, Channel $channel): bool
    {
        if ($channel->visibility->isPublic()) {
            return true;
        }

        return $this->isChannelMember->get($user, $channel);
    }

    /**
     * Determine whether the user can add members to the channel.
     */
    public function addMember(User $user, Channel $channel): bool
    {
        return $this->isChannelAdmin->get($user, $channel);
    }

    /**
     * Determine whether the user can remove members from the channel.
     */
    public function removeMember(User $user, Channel $channel): bool
    {
        return $this->isChannelAdmin->get($user, $channel);
    }
}
