<?php

declare(strict_types=1);

use App\Models\Message;
use App\Models\Thread;

test('to array', function (): void {
    $thread = Thread::factory()->create()->fresh();

    expect(array_keys($thread->toArray()))
        ->toBe([
            'id',
            'message_id',
            'created_at',
            'updated_at',
        ]);
});

test('belongs to op message', function (): void {
    $thread = Thread::factory()->create();

    expect($thread->message)->toBeInstanceOf(Message::class)
        ->and($thread->message->id)->toBe($thread->message_id);
});

test('has many replies', function (): void {
    $thread = Thread::factory()->create();
    $reply = Message::factory()->create(['thread_id' => $thread->id]);

    expect($thread->replies)->toHaveCount(1)
        ->and($thread->replies->first()->id)->toBe($reply->id);
});
