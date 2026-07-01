<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\UserTag;

final readonly class DetachUserTag
{
    public function handle(User $user, UserTag $tag): void
    {
        $tag->users()->detach($user->id);
    }
}
