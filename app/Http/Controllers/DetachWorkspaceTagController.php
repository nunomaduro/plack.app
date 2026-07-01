<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DetachWorkspaceTag;
use App\Http\Requests\DetachWorkspaceTagRequest;
use App\Models\Workspace;
use App\Models\WorkspaceTag;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class DetachWorkspaceTagController
{
    public function __invoke(
        DetachWorkspaceTagRequest $request,
        Workspace $workspace,
        WorkspaceTag $workspaceTag,
        DetachWorkspaceTag $detachWorkspaceTag,
    ): RedirectResponse {
        $detachWorkspaceTag->handle($workspace, $workspaceTag);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace tag detached.'),
        ]);

        return back();
    }
}
