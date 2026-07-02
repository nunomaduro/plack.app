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

it('attaches mentioned users to the message', function (): void {
    $channel = Channel::factory()->create();
    $sender = User::factory()->create();
    $mentioned = User::factory()->create(['username' => 'alice']);

    $message = resolve(SendMessage::class)->handle(
        $channel,
        $sender,
        'Hey @alice, check this out!',
    );

    expect($message->mentions)->toHaveCount(1)
        ->and($message->mentions->first()->id)->toBe($mentioned->id);
});

it('does not attach mentions when none exist', function (): void {
    $channel = Channel::factory()->create();
    $sender = User::factory()->create();

    $message = resolve(SendMessage::class)->handle(
        $channel,
        $sender,
        'No mentions here.',
    );

    expect($message->mentions)->toBeEmpty();
});
