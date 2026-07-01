<?php

declare(strict_types=1);

use App\Models\Workspace;

test('to array', function (): void {
    $workspace = Workspace::factory()->create()->fresh();

    expect(array_keys($workspace->toArray()))
        ->toBe([
            'id',
            'user_id',
            'name',
            'slug',
            'created_at',
            'updated_at',
        ]);
});
