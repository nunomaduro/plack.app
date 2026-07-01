<?php

declare(strict_types=1);

use App\Actions\PurgeExpiredWorkspaceInvitations;
use App\Models\WorkspaceInvitation;

it('deletes only expired invitations', function (): void {
    $active = WorkspaceInvitation::factory()->create();
    $expired = WorkspaceInvitation::factory()->expired()->create();

    resolve(PurgeExpiredWorkspaceInvitations::class)->handle();

    expect(WorkspaceInvitation::query()->whereKey($active->id)->exists())->toBeTrue()
        ->and(WorkspaceInvitation::query()->whereKey($expired->id)->exists())->toBeFalse();
});
