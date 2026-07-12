<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\MentionedInMessage;

it('returns the latest notifications for the authenticated user', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();
    $message = $channel->messages()->create([
        'user_id' => User::factory()->create()->id,
        'body' => 'Hey @'.str_replace(' ', '.', $user->name),
    ]);

    $user->notify(new MentionedInMessage($message));

    $this->actingAs($user)
        ->getJson(route('notifications.index'))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.data.channel_name', $channel->name);
});

it('marks a single notification as read', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();
    $message = $channel->messages()->create([
        'user_id' => User::factory()->create()->id,
        'body' => 'Hey there',
    ]);

    $user->notify(new MentionedInMessage($message));
    $notification = $user->notifications()->first();

    expect($notification->read_at)->toBeNull();

    $this->actingAs($user)
        ->patchJson(route('notifications.read', $notification->id))
        ->assertOk();

    expect($notification->fresh()->read_at)->not->toBeNull();
});

it('marks all notifications as read', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();
    $sender = User::factory()->create();

    for ($i = 0; $i < 3; $i++) {
        $message = $channel->messages()->create([
            'user_id' => $sender->id,
            'body' => "Message {$i}",
        ]);
        $user->notify(new MentionedInMessage($message));
    }

    expect($user->unreadNotifications()->count())->toBe(3);

    $this->actingAs($user)
        ->postJson(route('notifications.read-all'))
        ->assertOk();

    expect($user->unreadNotifications()->count())->toBe(0);
});

it('requires authentication to access notifications', function (): void {
    $this->getJson(route('notifications.index'))->assertUnauthorized();
    $this->postJson(route('notifications.read-all'))->assertUnauthorized();
});
