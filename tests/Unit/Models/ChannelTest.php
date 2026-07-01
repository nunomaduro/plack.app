<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\Workspace;

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

it('knows when it is the only channel in its workspace', function (): void {
    $channel = Channel::factory()->create();

    expect($channel->isOnlyChannelInWorkspace())->toBeTrue();
});

it('knows when it is not the only channel in its workspace', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    Channel::factory()->for($workspace)->create();

    expect($channel->isOnlyChannelInWorkspace())->toBeFalse();
});
