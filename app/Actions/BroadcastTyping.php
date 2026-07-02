<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\UserTyping;
use App\Models\Channel;
use App\Models\User;

final readonly class BroadcastTyping
{
    public function handle(Channel $channel, User $user): void
    {
        broadcast(new UserTyping($channel, $user))->toOthers();
    }
}
