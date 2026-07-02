<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\RegenerateWorkspaceJoinLink;
use App\Enums\WorkspaceType;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class RegenerateWorkspaceJoinLinkController
{
    public function __invoke(Workspace $workspace, RegenerateWorkspaceJoinLink $regenerateWorkspaceJoinLink): RedirectResponse
    {
        abort_unless($workspace->type === WorkspaceType::Public, 404);

        $regenerateWorkspaceJoinLink->handle($workspace);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Public join link regenerated.'),
        ]);

        return back();
    }
}
