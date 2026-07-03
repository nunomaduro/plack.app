<?php

declare(strict_types=1);

use App\Actions\CreateChannel;
use App\Enums\ChannelMemberRole;
use App\Enums\ChannelVisibility;
use App\Events\ChannelCreated;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Event;

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

it('adds the workspace owner as a channel admin', function (): void {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();

    $channel = resolve(CreateChannel::class)->handle($workspace, 'general');

    expect($channel->members()->whereKey($owner->id)->first()?->pivot->role)
        ->toBe(ChannelMemberRole::Admin->value);
});

it('broadcasts a channel created event on the workspace', function (): void {
    Event::fake([ChannelCreated::class]);

    $workspace = Workspace::factory()->create();

    $channel = resolve(CreateChannel::class)->handle(
        $workspace,
        'general',
    );

    Event::assertDispatched(
        ChannelCreated::class,
        fn (ChannelCreated $event): bool => $event->channel->is($channel)
            && $event->channel->workspace_id === $workspace->id,
    );
});
