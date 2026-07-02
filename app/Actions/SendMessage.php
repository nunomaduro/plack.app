<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class SendMessage
{
    public function __construct(private ParseMentions $parseMentions) {}

    public function handle(Channel $channel, User $sender, string $body): Message
    {
        return DB::transaction(function () use ($channel, $sender, $body): Message {
            $message = $channel->messages()->create([
                'user_id' => $sender->id,
                'body' => $body,
            ]);

            $mentionedUsers = $this->parseMentions->handle($body);

            if ($mentionedUsers->isNotEmpty()) {
                $message->mentions()->attach($mentionedUsers->pluck('id'));
            }

            return $message;
        });
    }
}
