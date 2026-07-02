<?php

declare(strict_types=1);

use App\Models\Message;
use App\Models\Reply;
use App\Models\User;

test('to array', function (): void {
    $reply = Reply::factory()->create()->fresh();

    expect(array_keys($reply->toArray()))
        ->toBe([
            'id',
            'message_id',
            'user_id',
            'body',
            'created_at',
            'updated_at',
        ]);
});

test('belongs to op message', function (): void {
    $message = Message::factory()->create();
    $reply = Reply::factory()->create(['message_id' => $message->id]);

    expect($reply->message)->toBeInstanceOf(Message::class)
        ->and($reply->message->id)->toBe($message->id);
});

test('belongs to a sender', function (): void {
    $user = User::factory()->create();
    $reply = Reply::factory()->create(['user_id' => $user->id]);

    expect($reply->sender)->toBeInstanceOf(User::class)
        ->and($reply->sender->id)->toBe($user->id);
});
