<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\WorkspaceTag;

final readonly class UpdateWorkspaceTag
{
    public function handle(WorkspaceTag $tag, string $name): void
    {
        $tag->update([
            'name' => $name,
        ]);
    }
}
