<?php

declare(strict_types=1);

use App\Actions\CreateWorkspace;
use App\Enums\WorkspaceType;
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
        ->and($workspace->name)->toBe('subscribe the channel')
        ->and($workspace->type)->toBe(WorkspaceType::Private)
        ->and($workspace->join_code)->toBeNull();
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

it('may create a public workspace with a join code', function (): void {
    $user = User::factory()->create();

    $workspace = resolve(CreateWorkspace::class)->handle(
        $user,
        'public-workspace',
        WorkspaceType::Public,
    );

    expect($workspace->type)->toBe(WorkspaceType::Public)
        ->and($workspace->join_code)->toBeString()
        ->and($workspace->join_code)->toHaveLength(64)
        ->and($workspace->channels()->sole()->name)->toBe('general');
});
