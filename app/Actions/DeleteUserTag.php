<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\UserTag;

final readonly class DeleteUserTag
{
    public function handle(UserTag $tag): void
    {
        $tag->delete();
    }
}
