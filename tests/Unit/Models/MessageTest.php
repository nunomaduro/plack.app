<?php

declare(strict_types=1);

use App\Models\Message;

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
