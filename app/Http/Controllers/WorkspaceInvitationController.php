<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteWorkspaceInvitation;
use App\Actions\InviteToWorkspace;
use App\Http\Requests\CreateWorkspaceInvitationRequest;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

final readonly class WorkspaceInvitationController
{
    public function store(
        CreateWorkspaceInvitationRequest $request,
        Workspace $workspace,
        #[CurrentUser] User $user,
        InviteToWorkspace $inviteToWorkspace,
    ): RedirectResponse {
        $inviteToWorkspace->handle($workspace, $request->string('email')->value(), $user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Invitation sent.'),
        ]);

        return back();
    }

    public function destroy(
        Workspace $workspace,
        WorkspaceInvitation $invitation,
        DeleteWorkspaceInvitation $deleteWorkspaceInvitation,
    ): RedirectResponse {
        abort_unless($invitation->workspace_id === $workspace->id, 404);

        $deleteWorkspaceInvitation->handle($invitation);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Invitation cancelled.'),
        ]);

        return back();
    }

    public function show(Request $request, WorkspaceInvitation $invitation): RedirectResponse
    {
        $user = $request->user();

        if ($user === null) {
            return to_route('register');
        }

        if ($user->email !== $invitation->email) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => __('This invitation was sent to a different email address.'),
            ]);
        }

        return to_route('workspace.index');
    }
}
