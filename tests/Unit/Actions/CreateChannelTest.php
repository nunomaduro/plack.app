<?php

declare(strict_types=1);

use App\Actions\CreateChannel;
use App\Events\ChannelCreated;
use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Support\Facades\Event;

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
