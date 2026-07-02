<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Inertia\Testing\AssertableInertia as Assert;

it('lets a member post a message to a channel', function (): void {
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $workspace->members()->attach($member);

    $response = $this->actingAs($member)->post(route('messages.store', [$workspace, $channel]), [
        'body' => 'Hello, world!',
    ]);

    $response->assertRedirectBack()
        ->assertSessionHasNoErrors();

    $message = $channel->messages()->sole();

    expect($message->body)->toBe('Hello, world!')
        ->and($message->sender->id)->toBe($member->id);
});

it('lets the owner post a message to a channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('messages.store', [$workspace, $channel]), [
        'body' => 'Hey team',
    ])->assertRedirectBack();

    expect($channel->messages()->count())->toBe(1);
});

it('requires a message body', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('messages.store', [$workspace, $channel]), [
        'body' => '',
    ])->assertSessionHasErrors('body');

    expect($channel->messages()->count())->toBe(0);
});

it('rejects a message body that is too long', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('messages.store', [$workspace, $channel]), [
        'body' => str_repeat('a', 1001),
    ])->assertSessionHasErrors('body');

    expect($channel->messages()->count())->toBe(0);
});

it('does not let a non-member post a message', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('messages.store', [$workspace, $channel]), [
        'body' => 'Let me in',
    ])->assertNotFound();

    expect($channel->messages()->count())->toBe(0);
});

it('cannot post a message to a channel from a different workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $otherWorkspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($otherWorkspace)->create();

    $this->actingAs($user)->post(route('messages.store', [$workspace, $channel]), [
        'body' => 'Wrong workspace',
    ])->assertNotFound();

    expect($channel->messages()->count())->toBe(0);
});

it('shows a channel with its messages', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('messages.store', [$workspace, $channel]), [
        'body' => 'First message',
    ]);

    $this->actingAs($user)->get(route('channel.show', [$workspace, $channel]))
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('channel/show')
            ->has('messages', 1)
            ->where('messages.0.body', 'First message')
            ->where('messages.0.sender', $user->name)
        );
});
