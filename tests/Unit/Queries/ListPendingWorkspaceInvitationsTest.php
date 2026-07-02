<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\WorkspaceInvitation;
use App\Queries\ListPendingWorkspaceInvitations;

it('returns pending invitations for the user with member counts', function (): void {
    $user = User::factory()->create();
    $invitation = WorkspaceInvitation::factory()->create(['email' => $user->email]);
    $invitation->workspace->members()->attach(User::factory()->create());

    $result = resolve(ListPendingWorkspaceInvitations::class)->get($user);

    expect($result->all())->toBe([
        [
            'code' => $invitation->code,
            'workspace' => [
                'id' => $invitation->workspace->id,
                'name' => $invitation->workspace->name,
                'memberCount' => 2,
            ],
            'invitedBy' => $invitation->inviter->name,
        ],
    ]);
});

it('ignores expired invitations and those for other users', function (): void {
    $user = User::factory()->create();
    WorkspaceInvitation::factory()->expired()->create(['email' => $user->email]);
    WorkspaceInvitation::factory()->create();

    expect(resolve(ListPendingWorkspaceInvitations::class)->get($user))->toBeEmpty();
});
