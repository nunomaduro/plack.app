<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workspace;
use App\Models\WorkspaceTag;

final readonly class AttachWorkspaceTag
{
    public function handle(Workspace $workspace, WorkspaceTag $tag): void
    {
        $workspace->tags()->syncWithoutDetaching([$tag->id]);
    }
}
