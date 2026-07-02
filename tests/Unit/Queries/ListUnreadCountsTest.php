<?php

declare(strict_types=1);

use App\Actions\MarkChannelAsRead;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use App\Queries\ListUnreadCounts;

it('counts all other-user messages in a never-read channel', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $user = User::factory()->create();

    Message::factory()->count(3)->for($channel)->create();

    $counts = resolve(ListUnreadCounts::class)->get($user, $workspace);

    expect($counts[$channel->id])->toBe(3);
});

it('returns zero after the channel is marked read', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $user = User::factory()->create();

    Message::factory()->count(3)->for($channel)->create();
    resolve(MarkChannelAsRead::class)->handle($channel, $user);

    $counts = resolve(ListUnreadCounts::class)->get($user, $workspace);

    expect($counts[$channel->id])->toBe(0);
});

it('counts only messages created after the read cursor', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $user = User::factory()->create();

    Message::factory()->for($channel)->create(['created_at' => now()->subHour()]);
    resolve(MarkChannelAsRead::class)->handle($channel, $user);
    Message::factory()->count(2)->for($channel)->create(['created_at' => now()->addHour()]);

    $counts = resolve(ListUnreadCounts::class)->get($user, $workspace);

    expect($counts[$channel->id])->toBe(2);
});

it('never counts the user\'s own messages', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $user = User::factory()->create();

    Message::factory()->count(4)->for($channel)->for($user, 'sender')->create();

    $counts = resolve(ListUnreadCounts::class)->get($user, $workspace);

    expect($counts[$channel->id])->toBe(0);
});

it('caps the unread count at 100', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $user = User::factory()->create();

    Message::factory()->count(105)->for($channel)->create();

    $counts = resolve(ListUnreadCounts::class)->get($user, $workspace);

    expect($counts[$channel->id])->toBe(100);
});

it('includes channels with no unread as zero', function (): void {
    $workspace = Workspace::factory()->create();
    $unread = Channel::factory()->for($workspace)->create();
    $empty = Channel::factory()->for($workspace)->create();
    $user = User::factory()->create();

    Message::factory()->for($unread)->create();

    $counts = resolve(ListUnreadCounts::class)->get($user, $workspace);

    expect($counts->keys()->all())->toContain($empty->id)
        ->and($counts[$empty->id])->toBe(0)
        ->and($counts[$unread->id])->toBe(1);
});
