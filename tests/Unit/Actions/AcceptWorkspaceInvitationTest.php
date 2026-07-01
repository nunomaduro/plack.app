<?php

declare(strict_types=1);

use App\Actions\AcceptWorkspaceInvitation;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;

it('adds the user as a member and deletes the invitation', function (): void {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->create();
    $invitation = WorkspaceInvitation::factory()->for($workspace)->create(['email' => $user->email]);

    resolve(AcceptWorkspaceInvitation::class)->handle($invitation, $user);

    expect($workspace->members()->whereKey($user->id)->exists())->toBeTrue()
        ->and(WorkspaceInvitation::query()->whereKey($invitation->id)->exists())->toBeFalse();
});
