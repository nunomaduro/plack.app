<?php

declare(strict_types=1);

use App\Actions\AddChannelMember;
use App\Enums\ChannelMemberRole;
use App\Models\Channel;
use App\Models\User;

it('adds a member with the member role by default', function (): void {
    $channel = Channel::factory()->create();
    $user = User::factory()->create();

    resolve(AddChannelMember::class)->handle($channel, $user);

    expect($channel->members()->whereKey($user->id)->first()?->pivot->role)
        ->toBe(ChannelMemberRole::Member->value);
});

it('adds a member with an explicit role', function (): void {
    $channel = Channel::factory()->create();
    $user = User::factory()->create();

    resolve(AddChannelMember::class)->handle($channel, $user, ChannelMemberRole::Admin);

    expect($channel->members()->whereKey($user->id)->first()?->pivot->role)
        ->toBe(ChannelMemberRole::Admin->value);
});

it('does not duplicate an existing member and updates their role', function (): void {
    $channel = Channel::factory()->create();
    $user = User::factory()->create();

    $action = resolve(AddChannelMember::class);
    $action->handle($channel, $user);
    $action->handle($channel, $user, ChannelMemberRole::Admin);

    expect($channel->members()->count())->toBe(1)
        ->and($channel->members()->whereKey($user->id)->first()?->pivot->role)
        ->toBe(ChannelMemberRole::Admin->value);
});
