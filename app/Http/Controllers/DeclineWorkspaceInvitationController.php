<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteWorkspaceInvitation;
use App\Http\Requests\RespondToWorkspaceInvitationRequest;
use App\Models\WorkspaceInvitation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class DeclineWorkspaceInvitationController
{
    public function __invoke(
        RespondToWorkspaceInvitationRequest $request,
        WorkspaceInvitation $invitation,
        DeleteWorkspaceInvitation $deleteWorkspaceInvitation,
    ): RedirectResponse {
        $deleteWorkspaceInvitation->handle($invitation);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Invitation declined.'),
        ]);

        return to_route('workspace.index');
    }
}
