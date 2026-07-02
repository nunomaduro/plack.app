<?php

declare(strict_types=1);

use App\Models\MessageMention;

test('to array', function (): void {
    $mention = MessageMention::factory()->create()->fresh();

    expect(array_keys($mention->toArray()))
        ->toBe([
            'id',
            'message_id',
            'user_id',
            'created_at',
            'updated_at',
        ]);
});
