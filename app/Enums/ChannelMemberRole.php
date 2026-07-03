<?php

declare(strict_types=1);

namespace App\Enums;

enum ChannelMemberRole: string
{
    case Admin = 'admin';
    case Member = 'member';

    public function isAdmin(): bool
    {
        return $this === ChannelMemberRole::Admin;
    }

    public function isMember(): bool
    {
        return $this === ChannelMemberRole::Member;
    }
}
