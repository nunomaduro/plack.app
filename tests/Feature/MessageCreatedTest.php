<?php

declare(strict_types=1);

use App\Events\MessageCreated;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;

it('broadcasts the channel and sender so the client can gate notifications', function (): void {
    $sender = User::factory()->create();
    $workspace = Workspace::factory()->for($sender, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();
    $message = Message::factory()->for($channel)->for($sender, 'sender')->create();

    expect(new MessageCreated($message)->broadcastWith())->toBe([
        'id' => $message->id,
        'channel_id' => $channel->id,
        'user_id' => $sender->id,
    ]);
});
