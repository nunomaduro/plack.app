<?php

declare(strict_types=1);

use App\Actions\UpdateChannel;
use App\Events\ChannelUpdated;
use App\Models\Channel;
use Illuminate\Support\Facades\Event;

it('may update a channel name', function (): void {
    $channel = Channel::factory()->create([
        'name' => 'general',
    ]);

    $action = resolve(UpdateChannel::class);

    $action->handle($channel, 'random');

    expect($channel->refresh()->name)->toBe('random');
});

it('broadcasts a channel updated event on the workspace', function (): void {
    Event::fake([ChannelUpdated::class]);

    $channel = Channel::factory()->create([
        'name' => 'general',
    ]);

    resolve(UpdateChannel::class)->handle($channel, 'random');

    Event::assertDispatched(
        ChannelUpdated::class,
        fn (ChannelUpdated $event): bool => $event->channel->is($channel)
            && $event->channel->workspace_id === $channel->workspace_id,
    );
});
