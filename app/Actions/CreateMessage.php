<?php

declare(strict_types=1);

namespace App\Actions;

use App\Events\MessageCreated;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class CreateMessage
{
    public function handle(Channel $channel, User $sender, string $body): Message
    {
        $message = DB::transaction(fn (): Message => $channel->messages()->create([
            'user_id' => $sender->id,
            'body' => $body,
        ]));

        broadcast(new MessageCreated($message))->toOthers();

        return $message;
    }
}
