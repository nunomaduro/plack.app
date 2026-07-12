<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\MessageCreated;
use App\Jobs\ProcessMessageMentions;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;

final readonly class CreateMessage
{
    public function handle(Channel $channel, User $sender, string $body): Message
    {
        $message = $channel->messages()->create([
            'user_id' => $sender->id,
            'body' => $body,
        ]);

        ProcessMessageMentions::dispatch($message);

        broadcast(new MessageCreated($message))->toOthers();

        return $message;
    }
}
