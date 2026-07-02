<?php

declare(strict_types=1);

use App\Actions\BroadcastTyping;
use App\Events\UserTyping;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('broadcasts a typing event for the given channel and user', function (): void {
    $user = User::factory()->create();
    $channel = Channel::factory()->create();

    Event::fake([UserTyping::class]);

    $action = resolve(BroadcastTyping::class);

    $action->handle($channel, $user);

    Event::assertDispatched(UserTyping::class, fn (UserTyping $event): bool => $event->channel->is($channel)
        && $event->user->is($user));
});
