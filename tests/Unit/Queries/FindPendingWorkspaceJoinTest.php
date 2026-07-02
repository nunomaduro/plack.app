<?php

declare(strict_types=1);

use App\Models\Workspace;
use App\Queries\FindPendingWorkspaceJoin;

it('returns public workspace details for a valid join code', function (): void {
    $workspace = Workspace::factory()->public()->create();

    $result = resolve(FindPendingWorkspaceJoin::class)->get($workspace->join_code);

    expect($result)->toBe([
        'code' => $workspace->join_code,
        'workspace' => [
            'id' => $workspace->id,
            'name' => $workspace->name,
        ],
    ]);
});

it('returns null for a private workspace join code', function (): void {
    $workspace = Workspace::factory()->private()->create([
        'join_code' => 'not-usable-for-private-workspaces',
    ]);

    expect(resolve(FindPendingWorkspaceJoin::class)->get($workspace->join_code))->toBeNull();
});

it('returns null for an unknown or unusable join code', function (): void {
    $query = resolve(FindPendingWorkspaceJoin::class);

    expect($query->get('missing'))->toBeNull()
        ->and($query->get(null))->toBeNull()
        ->and($query->get(''))->toBeNull()
        ->and($query->get(['array']))->toBeNull();
});

it('returns null when resolving an empty join code directly', function (): void {
    expect(resolve(FindPendingWorkspaceJoin::class)->workspace(''))->toBeNull();
});
