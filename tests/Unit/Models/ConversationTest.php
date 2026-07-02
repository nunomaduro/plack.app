<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\DirectMessage;
use App\Models\User;

test('to array', function (): void {
    $conversation = Conversation::factory()->create()->fresh();

    expect(array_keys($conversation->toArray()))
        ->toBe([
            'id',
            'created_at',
            'updated_at',
        ]);
});

it('may have participants', function (): void {
    $conversation = Conversation::factory()
        ->hasAttached(User::factory()->count(2), [], 'participants')
        ->create();

    expect($conversation->participants)->toHaveCount(2);
});

it('may have direct messages', function (): void {
    $conversation = Conversation::factory()
        ->has(DirectMessage::factory()->count(3), 'messages')
        ->create();

    expect($conversation->messages)->toHaveCount(3);
});
