<?php

declare(strict_types=1);

use App\Models\Conversation;
use App\Models\DirectMessage;
use App\Models\User;

test('to array', function (): void {
    $directMessage = DirectMessage::factory()->create()->fresh();

    expect(array_keys($directMessage->toArray()))
        ->toBe([
            'id',
            'conversation_id',
            'user_id',
            'body',
            'created_at',
            'updated_at',
        ]);
});

it('belongs to a conversation', function (): void {
    $directMessage = DirectMessage::factory()->create();

    expect($directMessage->conversation)
        ->toBeInstanceOf(Conversation::class);
});

it('has a sender', function (): void {
    $directMessage = DirectMessage::factory()->create();

    expect($directMessage->sender)
        ->toBeInstanceOf(User::class);
});
