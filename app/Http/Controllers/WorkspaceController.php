<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateWorkspace;
use App\Actions\DeleteWorkspace;
use App\Actions\UpdateWorkspace;
use App\Enums\WorkspaceType;
use App\Http\Requests\CreateWorkspaceRequest;
use App\Http\Requests\DeleteWorkspaceRequest;
use App\Http\Requests\UpdateWorkspaceRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class WorkspaceController
{
    public function index(#[CurrentUser] User $user): RedirectResponse|Response
    {
        $workspace = $user->ownedWorkspaces()->oldest()->first()
            ?? $user->memberWorkspaces()->oldest()->first();

        if ($workspace instanceof Workspace) {
            return to_route('workspace.show', $workspace);
        }

        return Inertia::render('workspace/empty');
    }

    public function show(Workspace $workspace): RedirectResponse
    {
        $channel = $workspace->channels()->latest()->firstOrFail();

        return to_route('channel.show', [$workspace, $channel]);
    }

    public function store(
        CreateWorkspaceRequest $request,
        #[CurrentUser] User $user,
        CreateWorkspace $createWorkspace,
    ): RedirectResponse {
        $name = $request->string('name')->value();
        $type = $request->enum('type', WorkspaceType::class, WorkspaceType::Private);

        $createWorkspace->handle($user, $name, $type);

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
        $slug = $request->string('slug')->value();

        $updateWorkspace->handle($workspace, $name, $slug);

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
