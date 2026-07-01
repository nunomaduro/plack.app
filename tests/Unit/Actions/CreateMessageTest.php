<?php

declare(strict_types=1);

use App\Actions\CreateMessage;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;

it('may create messages', function (): void {
    $channel = Channel::factory()->create();
    $user = User::factory()->create();

    $message = resolve(CreateMessage::class)->handle(
        $channel,
        $user,
        'Hello, world!',
    );

    expect($message)
        ->toBeInstanceOf(Message::class)
        ->and($message->channel->id)->toBe($channel->id)
        ->and($message->user->id)->toBe($user->id)
        ->and($message->body)->toBe('Hello, world!');
});
