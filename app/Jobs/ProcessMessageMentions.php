<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\ParseMentions;
use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class ProcessMessageMentions implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function handle(ParseMentions $parseMentions): void
    {
        $mentionedUsers = $parseMentions->handle($this->message->body);

        if ($mentionedUsers->isNotEmpty()) {
            $this->message->mentions()->attach($mentionedUsers->pluck('id'));
        }
    }
}
