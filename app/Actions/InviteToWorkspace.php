<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Notifications\WorkspaceInvitationNotification;
use Illuminate\Support\Facades\Notification;

final readonly class InviteToWorkspace
{
    public function handle(Workspace $workspace, string $email, User $inviter): WorkspaceInvitation
    {
        $invitation = $workspace->invitations()->create([
            'email' => $email,
            'invited_by' => $inviter->id,
            'expires_at' => now()->addDays(3),
        ]);

        Notification::route('mail', $email)->notify(new WorkspaceInvitationNotification($invitation));

        return $invitation;
    }
}
