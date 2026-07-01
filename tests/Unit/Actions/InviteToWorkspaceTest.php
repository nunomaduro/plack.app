<?php

declare(strict_types=1);

use App\Actions\InviteToWorkspace;
use App\Models\Workspace;
use App\Notifications\WorkspaceInvitationNotification;
use Illuminate\Support\Facades\Notification;

it('creates an invitation and notifies the invitee', function (): void {
    Notification::fake();

    $workspace = Workspace::factory()->create();

    $invitation = resolve(InviteToWorkspace::class)->handle(
        $workspace,
        'invitee@example.com',
        $workspace->owner,
    );

    expect($invitation->email)->toBe('invitee@example.com')
        ->and($invitation->invited_by)->toBe($workspace->owner->id)
        ->and($invitation->workspace_id)->toBe($workspace->id)
        ->and($invitation->expires_at->toDateTimeString())->toBe(now()->addDays(3)->toDateTimeString());

    Notification::assertSentOnDemand(WorkspaceInvitationNotification::class);
});
