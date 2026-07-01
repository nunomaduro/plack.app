<?php

declare(strict_types=1);

namespace App\Enums;

enum ChannelVisibility: string
{
    case Public = 'public';
    case Private = 'private';

    public function isPublic(): bool
    {
        return $this === ChannelVisibility::Public;
    }

    public function isPrivate(): bool
    {
        return $this === ChannelVisibility::Private;
    }
}
