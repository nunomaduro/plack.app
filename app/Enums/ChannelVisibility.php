<?php

declare(strict_types=1);

namespace App\Enums;

enum ChannelVisibility: string
{
    case Public = 'public';
    case Private = 'private';

    /**
     * @return array<int, array<string, string>>
     */
    public static function options(): array
    {
        return array_map(
            fn (ChannelVisibility $visibility): array => [$visibility->value => $visibility->name],
            ChannelVisibility::cases(),
        );
    }

    public function isPublic(): bool
    {
        return $this === ChannelVisibility::Public;
    }

    public function isPrivate(): bool
    {
        return $this === ChannelVisibility::Private;
    }
}
