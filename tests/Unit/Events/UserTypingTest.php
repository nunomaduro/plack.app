<?php

declare(strict_types=1);

use App\Events\UserTyping;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

it('broadcasts on the channel presence channel', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    $event = new UserTyping($channel, $user);

    $broadcastChannels = $event->broadcastOn();

    expect($broadcastChannels)->toHaveCount(1)
        ->and($broadcastChannels[0])->toBeInstanceOf(PresenceChannel::class)
        ->and($broadcastChannels[0]->name)->toBe('presence-channels.'.$channel->id);
});

it('broadcasts immediately', function (): void {
    expect(new UserTyping(Channel::factory()->create(), User::factory()->create()))
        ->toBeInstanceOf(ShouldBroadcastNow::class);
});

it('broadcasts as UserTyping', function (): void {
    $event = new UserTyping(Channel::factory()->create(), User::factory()->create());

    expect($event->broadcastAs())->toBe('UserTyping');
});

it('broadcasts the typing user', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    $event = new UserTyping($channel, $user);

    expect($event->broadcastWith())->toBe([
        'id' => $user->id,
        'name' => $user->name,
    ]);
});
