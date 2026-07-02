<?php

declare(strict_types=1);

use App\Models\Message;
use App\Models\Reply;

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
        ]);
});

test('has many replies', function (): void {
    $message = Message::factory()->create();
    $reply = Reply::factory()->create(['message_id' => $message->id]);

    expect($message->replies)->toHaveCount(1)
        ->and($message->replies->first()->id)->toBe($reply->id);
});

test('is a thread once it has a reply', function (): void {
    $message = Message::factory()->create();

    expect($message->isThread())->toBeFalse();

    Reply::factory()->create(['message_id' => $message->id]);

    expect($message->isThread())->toBeTrue();
});
