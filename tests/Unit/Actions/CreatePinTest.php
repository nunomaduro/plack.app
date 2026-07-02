<?php

declare(strict_types=1);

use App\Actions\CreatePin;
use App\Models\Channel;
use App\Models\Message;
use App\Models\Pin;
use App\Models\User;
use App\Models\Workspace;

it('may pin a message to a channel', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->for(Workspace::factory()->for($user, 'owner'))->create();
    $message = Message::factory()->for($channel)->for($user, 'sender')->create();

    $pin = resolve(CreatePin::class)->handle($user, $channel, $message);

    expect($pin)
        ->toBeInstanceOf(Pin::class)
        ->and($pin->user->id)->toBe($user->id)
        ->and($pin->channel->id)->toBe($channel->id)
        ->and($pin->message->id)->toBe($message->id);
});

it('does not duplicate a pin for the same message', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->for(Workspace::factory()->for($user, 'owner'))->create();
    $message = Message::factory()->for($channel)->for($user, 'sender')->create();

    $action = resolve(CreatePin::class);
    $first = $action->handle($user, $channel, $message);
    $second = $action->handle($user, $channel, $message);

    expect($first->id)->toBe($second->id)
        ->and(Pin::query()->count())->toBe(1);
});
