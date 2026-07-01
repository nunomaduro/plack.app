<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;

it('can post a message', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $response = $this->actingAs($user)->post(route('message.store', [$workspace, $channel]), [
        'body' => 'Hello, world!',
    ]);

    $response->assertRedirectBack();

    $messages = $channel->messages;

    expect($messages->count())->toBe(1)
        ->and($messages->first()->body)->toBe('Hello, world!')
        ->and($messages->first()->user->id)->toBe($user->id);
});

it('validates the message body', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('message.store', [$workspace, $channel]), [
        'body' => '',
    ])->assertSessionHasErrors('body');

    expect($channel->messages()->count())->toBe(0);
});

it('cannot post a message to a channel from a different workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $otherWorkspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($otherWorkspace)->create();

    $this->actingAs($user)->post(route('message.store', [$workspace, $channel]), [
        'body' => 'Hello, world!',
    ])->assertNotFound();

    expect($channel->messages()->count())->toBe(0);
});

it('cannot post a message to a workspace owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('message.store', [$workspace, $channel]), [
        'body' => 'Hello, world!',
    ])->assertNotFound();

    expect($channel->messages()->count())->toBe(0);
});
