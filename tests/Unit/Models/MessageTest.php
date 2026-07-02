<?php

declare(strict_types=1);

use App\Models\Message;
use App\Models\Thread;

test('to array', function (): void {
    $message = Message::factory()->create()->fresh();

    expect(array_keys($message->toArray()))
        ->toBe([
            'id',
            'channel_id',
            'user_id',
            'body',
            'created_at',
            'updated_at',
            'thread_id',
        ]);
});

test('belongs to a thread', function (): void {
    $thread = Thread::factory()->create();
    $message = Message::factory()->create(['thread_id' => $thread->id]);

    expect($message->thread)->toBeInstanceOf(Thread::class)
        ->and($message->thread->id)->toBe($thread->id);
});
