<?php

declare(strict_types=1);

use App\Models\Channel;

test('to array', function (): void {
    $channel = Channel::factory()->create()->fresh();

    expect(array_keys($channel->toArray()))
        ->toBe([
            'id',
            'workspace_id',
            'name',
            'visibility',
            'created_at',
            'updated_at',
        ]);
});
