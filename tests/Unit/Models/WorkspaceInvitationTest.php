<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;

test('to array', function (): void {
    $invitation = WorkspaceInvitation::factory()->create()->refresh();

    expect(array_keys($invitation->toArray()))
        ->toBe([
            'id',
            'workspace_id',
            'code',
            'email',
            'invited_by',
            'expires_at',
            'created_at',
            'updated_at',
        ]);
});

it('generates a unique code on creation', function (): void {
    $invitation = WorkspaceInvitation::factory()->create();

    expect($invitation->code)->toHaveLength(64);
});

it('knows when it is expired', function (): void {
    $active = WorkspaceInvitation::factory()->create();
    $expired = WorkspaceInvitation::factory()->expired()->create();

    expect($active->isExpired())->toBeFalse()
        ->and($expired->isExpired())->toBeTrue();
});

it('is bound by its code', function (): void {
    expect((new WorkspaceInvitation)->getRouteKeyName())->toBe('code');
});

it('belongs to a workspace and an inviter', function (): void {
    $workspace = Workspace::factory()->create();
    $inviter = User::factory()->create();

    $invitation = WorkspaceInvitation::factory()
        ->for($workspace)
        ->for($inviter, 'inviter')
        ->create();

    expect($invitation->workspace->id)->toBe($workspace->id)
        ->and($invitation->inviter->id)->toBe($inviter->id);
});
