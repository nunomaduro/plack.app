<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;

final readonly class CreateMessage
{
    public function handle(Channel $channel, User $user, string $body): Message
    {
        return $channel->messages()->create([
            'user_id' => $user->id,
            'body' => $body,
        ]);
    }
}
