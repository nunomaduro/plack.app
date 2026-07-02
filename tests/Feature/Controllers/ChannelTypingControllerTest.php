<?php

declare(strict_types=1);

use App\Events\UserTyping;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Event;

it('broadcasts a typing event for a member', function (): void {
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $workspace->members()->attach($member);

    Event::fake([UserTyping::class]);

    $this->actingAs($member)
        ->post(route('channel.typing', [$workspace, $channel]))
        ->assertNoContent();

    Event::assertDispatched(UserTyping::class, fn (UserTyping $event): bool => $event->channel->is($channel)
        && $event->user->is($member));
});

it('broadcasts a typing event for the owner', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    Event::fake([UserTyping::class]);

    $this->actingAs($user)
        ->post(route('channel.typing', [$workspace, $channel]))
        ->assertNoContent();

    Event::assertDispatched(UserTyping::class, fn (UserTyping $event): bool => $event->channel->is($channel)
        && $event->user->is($user));
});

it('does not broadcast a typing event for a non-member', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();

    Event::fake([UserTyping::class]);

    $this->actingAs($user)
        ->post(route('channel.typing', [$workspace, $channel]))
        ->assertNotFound();

    Event::assertNotDispatched(UserTyping::class);
});

it('does not broadcast a typing event for a channel from a different workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $otherWorkspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($otherWorkspace)->create();

    Event::fake([UserTyping::class]);

    $this->actingAs($user)
        ->post(route('channel.typing', [$workspace, $channel]))
        ->assertNotFound();

    Event::assertNotDispatched(UserTyping::class);
});

it('does not broadcast a typing event for a guest', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();

    Event::fake([UserTyping::class]);

    $this->post(route('channel.typing', [$workspace, $channel]))
        ->assertRedirect(route('login'));

    Event::assertNotDispatched(UserTyping::class);
});
