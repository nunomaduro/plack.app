<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\DirectMessage;
use App\Models\User;
use Inertia\Support\SessionKey;
use Inertia\Testing\AssertableInertia as Assert;

it('lists the user conversations', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $conversation = Conversation::factory()
        ->hasAttached($user, [], 'participants')
        ->hasAttached($otherUser, [], 'participants')
        ->create();

    $this->actingAs($user)->get(route('direct-message.index'))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('direct-message/index')
            ->has('conversations.data', 1)
        );
});

it('can show a conversation', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $conversation = Conversation::factory()
        ->hasAttached($user, [], 'participants')
        ->hasAttached($otherUser, [], 'participants')
        ->create();

    $message = DirectMessage::factory()
        ->for($conversation)
        ->for($otherUser, 'sender')
        ->create(['body' => 'Hello!']);

    $this->actingAs($user)->get(route('direct-message.show', $conversation))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('direct-message/show')
            ->where('conversation.id', $conversation->id)
            ->has('messages.data', 1)
        );
});

it('can start a conversation', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $this->actingAs($user)->post(route('direct-message.store'), [
        'user_id' => $otherUser->id,
    ])->assertRedirect();

    expect($user->conversations)->toHaveCount(1);
});

it('cannot start a conversation with oneself', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('direct-message.store'), [
        'user_id' => $user->id,
    ])->assertSessionHasErrors('user_id');
});

it('cannot view a conversation the user is not part of', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $conversation = Conversation::factory()
        ->hasAttached($otherUser, [], 'participants')
        ->create();

    $this->actingAs($user)
        ->get(route('direct-message.show', $conversation))
        ->assertNotFound();
});

it('can send a message in a conversation', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $conversation = Conversation::factory()
        ->hasAttached($user, [], 'participants')
        ->hasAttached($otherUser, [], 'participants')
        ->create();

    $this->actingAs($user)->post(route('direct-message.message.store', $conversation), [
        'body' => 'Hey there!',
    ])->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Message sent.'),
            ],
        ]);

    expect($conversation->messages()->count())->toBe(1)
        ->and($conversation->messages()->first()->body)->toBe('Hey there!');
});
