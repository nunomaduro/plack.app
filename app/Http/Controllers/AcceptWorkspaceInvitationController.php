<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AcceptPrivateWorkspaceInvitation;
use App\Http\Requests\RespondToWorkspaceInvitationRequest;
use App\Models\User;
use App\Models\WorkspaceInvitation;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class AcceptWorkspaceInvitationController
{
    public function __invoke(
        RespondToWorkspaceInvitationRequest $request,
        WorkspaceInvitation $invitation,
        #[CurrentUser] User $user,
        AcceptPrivateWorkspaceInvitation $acceptWorkspaceInvitation,
    ): RedirectResponse {
        $acceptWorkspaceInvitation->handle($invitation, $user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Invitation accepted.'),
        ]);

        return to_route('workspace.index');
    }
}
