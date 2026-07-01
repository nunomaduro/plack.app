<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateWorkspaceTag;
use App\Actions\DeleteWorkspaceTag;
use App\Actions\UpdateWorkspaceTag;
use App\Http\Requests\CreateWorkspaceTagRequest;
use App\Http\Requests\DeleteWorkspaceTagRequest;
use App\Http\Requests\UpdateWorkspaceTagRequest;
use App\Models\User;
use App\Models\WorkspaceTag;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class WorkspaceTagController
{
    public function index(#[CurrentUser] User $user): JsonResponse
    {
        return response()->json($user->workspaceTags);
    }

    public function store(
        CreateWorkspaceTagRequest $request,
        #[CurrentUser] User $user,
        CreateWorkspaceTag $createWorkspaceTag,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $createWorkspaceTag->handle($user, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace tag created.'),
        ]);

        return back();
    }

    public function update(
        UpdateWorkspaceTagRequest $request,
        WorkspaceTag $workspaceTag,
        UpdateWorkspaceTag $updateWorkspaceTag,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $updateWorkspaceTag->handle($workspaceTag, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace tag updated.'),
        ]);

        return back();
    }

    public function destroy(
        DeleteWorkspaceTagRequest $request,
        WorkspaceTag $workspaceTag,
        DeleteWorkspaceTag $deleteWorkspaceTag,
    ): RedirectResponse {
        $deleteWorkspaceTag->handle($workspaceTag);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace tag deleted.'),
        ]);

        return back();
    }
}
