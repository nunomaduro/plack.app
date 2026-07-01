<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateWorkspace;
use App\Http\Requests\CreateWorkspaceRequest;
use App\Models\User;
use App\Queries\ListWorkspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class WorkspaceController
{
    public function index(#[CurrentUser] User $user, ListWorkspace $listWorkspace): Response
    {
        $workspaces = $listWorkspace->get($user);

        return Inertia::render('workspace/list', [
            'workspaces' => $workspaces,
        ]);
    }

    public function store(
        CreateWorkspaceRequest $request,
        #[CurrentUser] User $user,
        CreateWorkspace $createWorkspace,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $createWorkspace->handle($user, $name);

        return back();
    }
}
