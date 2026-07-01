<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Message;
use App\Models\Pin;
use App\Models\User;

final readonly class CreatePin
{
    public function handle(User $user, Channel $channel, Message $message): Pin
    {
        return $channel->pins()->firstOrCreate([
            'message_id' => $message->id,
        ], [
            'user_id' => $user->id,
        ]);
    }
}
