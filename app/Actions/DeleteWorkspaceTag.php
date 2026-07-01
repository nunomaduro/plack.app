<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\WorkspaceTag;

final readonly class DeleteWorkspaceTag
{
    public function handle(WorkspaceTag $tag): void
    {
        $tag->delete();
    }
}
