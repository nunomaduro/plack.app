<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateWorkspace;
use App\Actions\DeleteWorkspace;
use App\Actions\UpdateWorkspace;
use App\Http\Requests\CreateWorkspaceRequest;
use App\Http\Requests\DeleteWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Queries\ListOwnedWorkspaces;
use App\Queries\ListPendingWorkspaceInvitations;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class WorkspaceController
{
    public function index(
        #[CurrentUser] User $user,
        ListOwnedWorkspaces $listOwnedWorkspaces,
        ListPendingWorkspaceInvitations $listPendingWorkspaceInvitations,
    ): Response {
        return Inertia::render('workspace/list', [
            'ownedWorkspaces' => $listOwnedWorkspaces->get($user),
            'memberWorkspaces' => $user->memberWorkspaces()->get(['workspaces.id', 'workspaces.name']),
            'pendingInvitations' => $listPendingWorkspaceInvitations->get($user),
        ]);
    }

    public function show(Workspace $workspace): Response
    {
        $workspace->load(['owner', 'members', 'invitations']);

        return Inertia::render('workspace/settings', [
            'workspace' => $workspace->only('id', 'name'),
            'owner' => $workspace->owner->only('id', 'name', 'email'),
            'members' => $workspace->members->map->only('id', 'name', 'email')->values(),
            'invitations' => $workspace->invitations->map(fn (WorkspaceInvitation $invitation): array => [
                'code' => $invitation->code,
                'email' => $invitation->email,
            ])->values(),
        ]);
    }

    public function store(
        CreateWorkspaceRequest $request,
        #[CurrentUser] User $user,
        CreateWorkspace $createWorkspace,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $createWorkspace->handle($user, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace created.'),
        ]);

        return back();
    }

    public function update(
        UpdateWorkspaceRequest $request,
        Workspace $workspace,
        UpdateWorkspace $updateWorkspace,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $updateWorkspace->handle($workspace, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace updated.'),
        ]);

        return back();
    }

    public function destroy(
        DeleteWorkspaceRequest $request,
        Workspace $workspace,
        DeleteWorkspace $deleteWorkspace,
    ): RedirectResponse {
        $deleteWorkspace->handle($workspace);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace deleted.'),
        ]);

        return to_route('workspace.index');
    }
}
