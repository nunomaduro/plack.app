<?php

declare(strict_types=1);

use App\Actions\UpdateChannel;
use App\Enums\ChannelVisibility;
use App\Events\ChannelUpdated;
use App\Models\Channel;
use Illuminate\Support\Facades\Event;

it('may update a channel name and visibility', function (): void {
    $channel = Channel::factory()->public()->create([
        'name' => 'general',
    ]);

    $action = resolve(UpdateChannel::class);

    $action->handle($channel, 'random', ChannelVisibility::Private);

    expect($channel->refresh()->name)->toBe('random')
        ->and($channel->visibility)->toBe(ChannelVisibility::Private);
});

it('broadcasts a channel updated event on the workspace', function (): void {
    Event::fake([ChannelUpdated::class]);

    $channel = Channel::factory()->create([
        'name' => 'general',
    ]);

    resolve(UpdateChannel::class)->handle($channel, 'random', ChannelVisibility::Private);

    Event::assertDispatched(
        ChannelUpdated::class,
        fn (ChannelUpdated $event): bool => $event->channel->is($channel)
            && $event->channel->workspace_id === $channel->workspace_id,
    );
});
