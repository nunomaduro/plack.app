<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AttachWorkspaceTag;
use App\Http\Requests\AttachWorkspaceTagRequest;
use App\Models\Workspace;
use App\Models\WorkspaceTag;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class AttachWorkspaceTagController
{
    public function __invoke(
        AttachWorkspaceTagRequest $request,
        Workspace $workspace,
        WorkspaceTag $workspaceTag,
        AttachWorkspaceTag $attachWorkspaceTag,
    ): RedirectResponse {
        $attachWorkspaceTag->handle($workspace, $workspaceTag);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace tag attached.'),
        ]);

        return back();
    }
}
