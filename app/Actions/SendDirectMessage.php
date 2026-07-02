<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Conversation;
use App\Models\DirectMessage;
use App\Models\User;

final readonly class SendDirectMessage
{
    public function handle(Conversation $conversation, User $sender, string $body): DirectMessage
    {
        return $conversation->messages()->create([
            'user_id' => $sender->id,
            'body' => $body,
        ]);
    }
}
