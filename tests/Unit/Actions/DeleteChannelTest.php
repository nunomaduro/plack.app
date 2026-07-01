<?php

declare(strict_types=1);

use App\Actions\DeleteChannel;
use App\Models\Channel;
use App\Models\Message;

it('may delete a channel', function (): void {
    $channel = Channel::factory()->create();

    $action = resolve(DeleteChannel::class);

    $action->handle($channel);

    expect(Channel::query()->whereKey($channel->id)->exists())->toBeFalse();
});

it('deletes the channel messages', function (): void {
    $channel = Channel::factory()->create();
    $messages = Message::factory()->count(3)->for($channel)->create();

    $action = resolve(DeleteChannel::class);

    $action->handle($channel);

    expect(Channel::query()->whereKey($channel->id)->exists())->toBeFalse();

    $messages->each(function (Message $message): void {
        expect(Message::query()->whereKey($message->id)->exists())->toBeFalse();
    });
});
