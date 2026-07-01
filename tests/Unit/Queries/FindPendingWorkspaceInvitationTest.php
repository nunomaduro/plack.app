<?php

declare(strict_types=1);

use App\Models\WorkspaceInvitation;
use App\Queries\FindPendingWorkspaceInvitation;

it('returns the invitation details for a valid code', function (): void {
    $invitation = WorkspaceInvitation::factory()->create();

    $result = resolve(FindPendingWorkspaceInvitation::class)->get($invitation->code);

    expect($result)->toBe([
        'code' => $invitation->code,
        'workspace' => $invitation->workspace->name,
    ]);
});

it('returns null for an expired invitation', function (): void {
    $invitation = WorkspaceInvitation::factory()->expired()->create();

    expect(resolve(FindPendingWorkspaceInvitation::class)->get($invitation->code))->toBeNull();
});

it('returns null for an unknown code', function (): void {
    expect(resolve(FindPendingWorkspaceInvitation::class)->get('missing'))->toBeNull();
});

it('returns null when the code is not a usable string', function (): void {
    $query = resolve(FindPendingWorkspaceInvitation::class);

    expect($query->get(null))->toBeNull()
        ->and($query->get(''))->toBeNull()
        ->and($query->get(['array']))->toBeNull();
});
