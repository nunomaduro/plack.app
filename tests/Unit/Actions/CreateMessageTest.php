<?php

declare(strict_types=1);

use App\Actions\CreateMessage;
use App\Events\MessageCreated;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('may send messages', function (): void {
    Event::fake([MessageCreated::class]);

    $channel = Channel::factory()->create();
    $sender = User::factory()->create();

    $message = resolve(CreateMessage::class)->handle(
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

it('broadcasts a message created event on the channel', function (): void {
    Event::fake([MessageCreated::class]);

    $channel = Channel::factory()->create();
    $sender = User::factory()->create();

    $message = resolve(CreateMessage::class)->handle(
        $channel,
        $sender,
        'Hello, world!',
    );

    Event::assertDispatched(
        MessageCreated::class,
        fn (MessageCreated $event): bool => $event->message->is($message)
            && $event->message->channel_id === $channel->id,
    );
});
