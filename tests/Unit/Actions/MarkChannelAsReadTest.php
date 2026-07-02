<?php

declare(strict_types=1);

use App\Actions\MarkChannelAsRead;
use App\Models\Channel;
use App\Models\ChannelMember;
use App\Models\User;

it('records a read cursor for the user and channel', function (): void {
    $channel = Channel::factory()->create();
    $user = User::factory()->create();

    resolve(MarkChannelAsRead::class)->handle($channel, $user);

    $read = ChannelMember::query()
        ->where('user_id', $user->id)
        ->where('channel_id', $channel->id)
        ->sole();

    expect($read->last_read_at)->not->toBeNull();
});

it('updates the existing cursor instead of duplicating', function (): void {
    $channel = Channel::factory()->create();
    $user = User::factory()->create();

    $action = resolve(MarkChannelAsRead::class);
    $action->handle($channel, $user);
    $action->handle($channel, $user);

    expect(ChannelMember::query()->where('user_id', $user->id)->where('channel_id', $channel->id)->count())
        ->toBe(1);
});
