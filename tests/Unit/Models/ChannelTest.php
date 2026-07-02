<?php

declare(strict_types=1);

use App\Models\Channel;

test('to array', function (): void {
    $workspace = Channel::factory()->create()->fresh();

    expect(array_keys($workspace->toArray()))
        ->toBe([
            'id',
            'workspace_id',
            'name',
            'created_at',
            'updated_at',
            'slug',
        ]);
});
