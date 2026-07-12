<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class MentionedInMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Message $message) {}

    /**
     * @return list<string>
     */
    public function via(): array
    {
        return ['database'];
    }

    /**
     * @return array{message_id: string, channel_id: string, channel_name: string, workspace_slug: string, sender_name: string, body_preview: string}
     */
    public function toArray(): array
    {
        $channel = $this->message->channel;

        return [
            'message_id' => $this->message->id,
            'channel_id' => $channel->id,
            'channel_name' => $channel->name,
            'workspace_slug' => $channel->workspace->slug,
            'sender_name' => $this->message->sender->name,
            'body_preview' => str($this->message->body)->limit(80)->value(),
        ];
    }
}
