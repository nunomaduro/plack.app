<?php

declare(strict_types=1);

use App\Actions\CreateOrGetConversation;
use App\Models\Conversation;
use App\Models\User;

it('may create a conversation between two users', function (): void {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $conversation = resolve(CreateOrGetConversation::class)->handle($userA, $userB);

    expect($conversation)
        ->toBeInstanceOf(Conversation::class)
        ->and($conversation->participants)->toHaveCount(2)
        ->and($conversation->participants->pluck('id')->sort()->values()->toArray())
        ->toBe([$userA->id, $userB->id]);
});

it('returns the existing conversation between the same two users', function (): void {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $first = resolve(CreateOrGetConversation::class)->handle($userA, $userB);
    $second = resolve(CreateOrGetConversation::class)->handle($userA, $userB);

    expect($second->id)->toBe($first->id)
        ->and($second->participants->count())->toBe(2);
});

it('may create a conversation with the same user', function (): void {
    $user = User::factory()->create();

    $conversation = resolve(CreateOrGetConversation::class)->handle($user, $user);

    expect($conversation)
        ->toBeInstanceOf(Conversation::class)
        ->and($conversation->participants)->toHaveCount(1);
});
