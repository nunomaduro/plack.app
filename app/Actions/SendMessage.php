<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;

final readonly class SendMessage
{
    public function handle(Channel $channel, User $sender, string $body): Message
    {
        return $channel->messages()->create([
            'user_id' => $sender->id,
            'body' => $body,
        ]);
    }
}
