<?php

declare(strict_types=1);

use App\Actions\CreateWorkspace;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;

it('may create workspaces', function (): void {
    $user = User::factory()->create();

    $workspace = resolve(CreateWorkspace::class)->handle(
        $user,
        'subscribe the channel',
    );

    expect($workspace)
        ->toBeInstanceOf(Workspace::class)
        ->and($workspace->owner->id)->toBe($user->id)
        ->and($workspace->name)->toBe('subscribe the channel');
});

it('bootstraps a general channel', function (): void {
    $user = User::factory()->create();

    $workspace = resolve(CreateWorkspace::class)->handle(
        $user,
        'test-workspace',
    );

    $channel = $workspace->channels()->sole();

    expect($channel)
        ->toBeInstanceOf(Channel::class)
        ->and($channel->name)->toBe('general');
});
