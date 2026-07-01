<?php

declare(strict_types=1);

use App\Actions\CreateChannel;
use App\Enums\ChannelVisibility;
use App\Models\Channel;
use App\Models\Workspace;

it('may create a public channel', function (): void {
    $workspace = Workspace::factory()->create();

    $channel = resolve(CreateChannel::class)->handle(
        $workspace,
        'barrentix',
        ChannelVisibility::Public
    );

    expect($channel)->toBeInstanceOf(Channel::class)
        ->and($channel->visibility)->toBeInstanceOf(ChannelVisibility::class)
        ->and($channel->visibility)->toBe(ChannelVisibility::Public);
});

it('may create a private channel', function (): void {

    $workspace = Workspace::factory()->create();

    $channel = resolve(CreateChannel::class)->handle(
        $workspace,
        'barrentix',
        ChannelVisibility::Private
    );

    expect($channel)->toBeInstanceOf(Channel::class)
        ->and($channel->visibility)->toBeInstanceOf(ChannelVisibility::class)
        ->and($channel->visibility)->toBe(ChannelVisibility::Private);

});

it('may create channels', function (): void {
    $workspace = Workspace::factory()->create();

    $channel = resolve(CreateChannel::class)->handle(
        $workspace,
        'general',
    );

    expect($channel)
        ->toBeInstanceOf(Channel::class)
        ->and($channel->workspace->id)->toBe($workspace->id)
        ->and($channel->name)->toBe('general');
});
