<?php

declare(strict_types=1);

use App\Actions\JoinWorkspace;
use App\Models\User;
use App\Models\Workspace;

it('adds the user as a workspace member', function (): void {
    $workspace = Workspace::factory()->public()->create();
    $user = User::factory()->create();

    resolve(JoinWorkspace::class)->handle($workspace, $user);

    expect($workspace->members()->whereKey($user->id)->exists())->toBeTrue();
});

it('is idempotent for existing members', function (): void {
    $workspace = Workspace::factory()->public()->create();
    $user = User::factory()->create();

    resolve(JoinWorkspace::class)->handle($workspace, $user);
    resolve(JoinWorkspace::class)->handle($workspace, $user);

    expect($workspace->members()->whereKey($user->id)->count())->toBe(1);
});
