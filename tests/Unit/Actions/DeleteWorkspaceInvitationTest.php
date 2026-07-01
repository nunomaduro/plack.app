<?php

declare(strict_types=1);

use App\Actions\DeleteWorkspaceInvitation;
use App\Models\WorkspaceInvitation;

it('deletes the invitation', function (): void {
    $invitation = WorkspaceInvitation::factory()->create();

    resolve(DeleteWorkspaceInvitation::class)->handle($invitation);

    expect(WorkspaceInvitation::query()->count())->toBe(0);
});
