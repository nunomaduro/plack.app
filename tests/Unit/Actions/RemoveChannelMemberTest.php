<?php

declare(strict_types=1);

use App\Actions\RemoveChannelMember;
use App\Enums\ChannelMemberRole;
use App\Models\Channel;
use App\Models\User;

it('removes a member from a channel', function (): void {
    $channel = Channel::factory()->create();
    $user = User::factory()->create();
    $channel->members()->attach($user, ['role' => ChannelMemberRole::Member->value]);

    resolve(RemoveChannelMember::class)->handle($channel, $user);

    expect($channel->members()->whereKey($user->id)->exists())->toBeFalse();
});
