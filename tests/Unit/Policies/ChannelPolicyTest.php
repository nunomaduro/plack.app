<?php

declare(strict_types=1);

use App\Enums\ChannelMemberRole;
use App\Models\Channel;
use App\Models\User;
use App\Policies\ChannelPolicy;

beforeEach(function (): void {
    $this->policy = resolve(ChannelPolicy::class);
});

it('lets anyone view a public channel', function (): void {
    $channel = Channel::factory()->public()->create();
    $user = User::factory()->create();

    expect($this->policy->view($user, $channel))->toBeTrue();
});

it('lets a member view a private channel', function (): void {
    $channel = Channel::factory()->private()->create();
    $user = User::factory()->create();
    $channel->members()->attach($user, ['role' => ChannelMemberRole::Member->value]);

    expect($this->policy->view($user, $channel))->toBeTrue();
});

it('does not let a non-member view a private channel', function (): void {
    $channel = Channel::factory()->private()->create();
    $user = User::factory()->create();

    expect($this->policy->view($user, $channel))->toBeFalse();
});

it('only lets admins add and remove members', function (): void {
    $channel = Channel::factory()->private()->create();
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $channel->members()->attach($admin, ['role' => ChannelMemberRole::Admin->value]);
    $channel->members()->attach($member, ['role' => ChannelMemberRole::Member->value]);

    expect($this->policy->addMember($admin, $channel))->toBeTrue()
        ->and($this->policy->removeMember($admin, $channel))->toBeTrue()
        ->and($this->policy->addMember($member, $channel))->toBeFalse()
        ->and($this->policy->removeMember($member, $channel))->toBeFalse();
});
