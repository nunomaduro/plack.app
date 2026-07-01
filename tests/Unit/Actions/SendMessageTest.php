<?php

declare(strict_types=1);

use App\Actions\SendMessage;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;

it('may send messages', function (): void {
    $channel = Channel::factory()->create();
    $sender = User::factory()->create();

    $message = resolve(SendMessage::class)->handle(
        $channel,
        $sender,
        'Hello, world!',
    );

    expect($message)
        ->toBeInstanceOf(Message::class)
        ->and($message->channel->id)->toBe($channel->id)
        ->and($message->sender->id)->toBe($sender->id)
        ->and($message->body)->toBe('Hello, world!');
});
